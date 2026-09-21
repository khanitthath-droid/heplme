<?php

namespace App\Services;

use App\Models\Book;
use App\Models\WantedBook;
use App\Models\ExchangeRequest;
use App\Models\TradeRing;
use App\Models\User;

class MatchingService
{
    /**
     * ตรวจสอบว่าหนังสือที่มี กับ หนังสือที่ตามหา มีความตรงกันหรือไม่
     * ปลอดภัยจาก Null/Empty Strings, ตรวจสอบสองทิศทาง และแยกคำสำคัญ
     */
    public static function isMatch(?string $bookTitle, ?string $bookAuthor, ?string $wantedTitle, ?string $wantedAuthor): bool
    {
        $bookTitle = trim(mb_strtolower($bookTitle ?? ''));
        $wantedTitle = trim(mb_strtolower($wantedTitle ?? ''));
        $bookAuthor = trim(mb_strtolower($bookAuthor ?? ''));
        $wantedAuthor = trim(mb_strtolower($wantedAuthor ?? ''));

        // 1. ตรวจสอบชื่อหนังสือ (Title Matching)
        if (!empty($bookTitle) && !empty($wantedTitle)) {
            // เปรียบเทียบแบบสองทิศทาง (Substrings)
            if (mb_stripos($bookTitle, $wantedTitle) !== false || mb_stripos($wantedTitle, $bookTitle) !== false) {
                return true;
            }

            // ตัดคำขยาย/วงเล็บ เช่น "Harry Potter (แฮร์รี่ พอตเตอร์)" -> เปรียบเทียบแต่ละส่วน
            $bookClean = preg_replace('/[()\[\]\/,–—-]/u', ' ', $bookTitle);
            $wantedClean = preg_replace('/[()\[\]\/,–—-]/u', ' ', $wantedTitle);

            $bookWords = array_filter(explode(' ', $bookClean), fn($w) => mb_strlen(trim($w)) >= 3);
            $wantedWords = array_filter(explode(' ', $wantedClean), fn($w) => mb_strlen(trim($w)) >= 3);

            if (!empty($bookWords) && !empty($wantedWords)) {
                $intersect = array_intersect($bookWords, $wantedWords);
                if (count($intersect) >= 2 || (count($wantedWords) === 1 && count($intersect) === 1)) {
                    return true;
                }
            }
        }

        // 2. ตรวจสอบชื่อผู้แต่ง (Author Matching) - ต้องมีความยาวอย่างน้อย 3 ตัวอักษร
        if (!empty($bookAuthor) && !empty($wantedAuthor) && mb_strlen($bookAuthor) >= 3 && mb_strlen($wantedAuthor) >= 3) {
            if (mb_stripos($bookAuthor, $wantedAuthor) !== false || mb_stripos($wantedAuthor, $bookAuthor) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * ดึงโครงสร้าง Directed Graph การส่งมอบหนังสือ
     * Edge: User A -> User B หมายถึง User A มีหนังสือที่ User B ต้องการ
     */
    public static function buildExchangeGraph()
    {
        $books = Book::where('status', 'available')
            ->with('user')
            ->get();

        $wantedBooks = WantedBook::with('user')->get();

        $edges = []; // $edges[giver_user_id][receiver_user_id][] = ['book' => $book, 'wanted' => $wanted]

        foreach ($books as $book) {
            foreach ($wantedBooks as $wanted) {
                if ($book->user_id === $wanted->user_id) {
                    continue;
                }

                if (self::isMatch($book->title, $book->author, $wanted->title, $wanted->author)) {
                    $edges[$book->user_id][$wanted->user_id][] = [
                        'giver' => $book->user,
                        'receiver' => $wanted->user,
                        'book' => $book,
                        'wanted' => $wanted,
                    ];
                }
            }
        }

        return $edges;
    }

    /**
     * ดึงรายการจับคู่ตรง 2 ทาง (Direct 2-Way Matches) สำหรับผู้ใช้ที่ระบุ
     */
    public static function getTwoWayMatches(User $user): array
    {
        $edges = self::buildExchangeGraph();
        $userId = $user->id;

        $matches = [];
        $seenPairs = [];

        if (!isset($edges[$userId])) {
            return [];
        }

        // ดึง ExchangeRequests ที่เกี่ยวข้องเพื่อนำมาแสดงสถานะคำขอ
        $existingRequests = ExchangeRequest::where(function ($q) use ($userId) {
            $q->where('requester_id', $userId)
              ->orWhere('receiver_id', $userId);
        })->get();

        foreach ($edges[$userId] as $partnerId => $myGiveItems) {
            if (isset($edges[$partnerId][$userId])) {
                $partnerGiveItems = $edges[$partnerId][$userId];

                foreach ($myGiveItems as $myGive) {
                    foreach ($partnerGiveItems as $partnerGive) {
                        $pairKey = $myGive['book']->id . '_' . $partnerGive['book']->id;
                        if (isset($seenPairs[$pairKey])) {
                            continue;
                        }
                        $seenPairs[$pairKey] = true;

                        // ตรวจสอบสถานะคำขอแลกเปลี่ยนที่มีอยู่ระหว่างคู่นี้
                        $existingRequest = $existingRequests->first(function ($req) use ($myGive, $partnerGive, $userId, $partnerId) {
                            return (
                                ($req->offered_book_id == $myGive['book']->id && $req->requested_book_id == $partnerGive['book']->id) ||
                                ($req->offered_book_id == $partnerGive['book']->id && $req->requested_book_id == $myGive['book']->id)
                            );
                        });

                        $matches[] = [
                            'my_book' => $myGive['book'],
                            'my_wanted' => $partnerGive['wanted'],
                            'other_book' => $partnerGive['book'],
                            'other_wanted' => $myGive['wanted'],
                            'other_user' => $myGive['receiver'],
                            'existing_request' => $existingRequest,
                        ];
                    }
                }
            }
        }

        return $matches;
    }

    /**
     * ดึงรายการจับคู่ลูกโซ่ 3 ทาง (3-Way Circular Matches / Trade Rings) สำหรับผู้ใช้ที่ระบุ
     * Loop: User (A) -> Partner 1 (B) -> Partner 2 (C) -> User (A)
     */
    public static function getThreeWayMatches(User $user): array
    {
        $edges = self::buildExchangeGraph();
        $userId = $user->id;

        if (!isset($edges[$userId])) {
            return [];
        }

        // ดึง TradeRing ที่ผู้ใช้ปัจจุบันมีส่วนร่วม
        $existingTradeRings = TradeRing::where(function ($q) use ($userId) {
            $q->where('user1_id', $userId)
              ->orWhere('user2_id', $userId)
              ->orWhere('user3_id', $userId);
        })->get();

        $matches = [];
        $seenCycles = [];

        // Loop: A -> B -> C -> A
        $u1 = $userId;
        foreach ($edges[$u1] as $u2 => $items12) {
            if ($u2 === $u1 || !isset($edges[$u2])) {
                continue;
            }

            foreach ($edges[$u2] as $u3 => $items23) {
                if ($u3 === $u1 || $u3 === $u2 || !isset($edges[$u3][$u1])) {
                    continue;
                }

                $items31 = $edges[$u3][$u1];

                foreach ($items12 as $i12) {
                    foreach ($items23 as $i23) {
                        foreach ($items31 as $i31) {
                            $cycleKey = "{$i12['book']->id}_{$i23['book']->id}_{$i31['book']->id}";
                            if (isset($seenCycles[$cycleKey])) {
                                continue;
                            }
                            $seenCycles[$cycleKey] = true;

                            $bookIds = [$i12['book']->id, $i23['book']->id, $i31['book']->id];

                            // ค้นหา TradeRing ที่ตรงกับหนังสือ 3 เล่มนี้
                            $existingRing = $existingTradeRings->first(function ($ring) use ($bookIds) {
                                return in_array($ring->book1_id, $bookIds) &&
                                       in_array($ring->book2_id, $bookIds) &&
                                       in_array($ring->book3_id, $bookIds);
                            });

                            $matches[] = [
                                // ฝั่งผู้ใช้ปัจจุบัน (User A)
                                'my_book' => $i12['book'],           // หนังสือที่ฉันมอบให้ Partner 1 (User B)
                                'my_receive_book' => $i31['book'],   // หนังสือที่ฉันจะได้รับจาก Partner 2 (User C)
                                'my_wanted' => $i31['wanted'],       // ความต้องการของฉันที่ได้รับการตอบสนอง

                                // Partner 1 (User B) - คนที่รับหนังสือจากฉัน และส่งต่อให้ Partner 2
                                'partner1_user' => $i12['receiver'],
                                'partner1_receive_book' => $i12['book'],
                                'partner1_give_book' => $i23['book'],
                                'partner1_wanted' => $i12['wanted'],

                                // Partner 2 (User C) - คนที่รับหนังสือจาก Partner 1 และส่งต่อกลับมาให้ฉัน
                                'partner2_user' => $i23['receiver'],
                                'partner2_receive_book' => $i23['book'],
                                'partner2_give_book' => $i31['book'],
                                'partner2_wanted' => $i23['wanted'],

                                // ข้อมูลสถานะคำขอวงจร 3 ฝ่าย
                                'existing_trade_ring' => $existingRing,
                                'user_status' => $existingRing ? $existingRing->getUserStatus($userId) : null,
                                'accepted_count' => $existingRing ? $existingRing->acceptedCount() : 0,
                            ];
                        }
                    }
                }
            }
        }

        return $matches;
    }

    /**
     * ดึงรายการจับคู่แบบ 1 ทาง (One-Way Wishlist & Demand)
     */
    public static function getOneWayMatches(User $user): array
    {
        $userId = $user->id;

        // 1. หนังสือที่ฉันต้องการ และมีคนลงพร้อมแลกอยู่ในระบบ
        $myWanted = WantedBook::where('user_id', $userId)->get();
        $availableOtherBooks = Book::where('user_id', '!=', $userId)
            ->where('status', 'available')
            ->with('user')
            ->get();

        $matchingWishlist = [];
        $seenWishlist = [];
        foreach ($myWanted as $wanted) {
            foreach ($availableOtherBooks as $book) {
                if (self::isMatch($book->title, $book->author, $wanted->title, $wanted->author)) {
                    $key = $wanted->id . '_' . $book->id;
                    if (!isset($seenWishlist[$key])) {
                        $seenWishlist[$key] = true;
                        $matchingWishlist[] = [
                            'wanted' => $wanted,
                            'available_book' => $book,
                            'owner' => $book->user,
                        ];
                    }
                }
            }
        }

        // 2. มีสมาชิกคนอื่นกำลังตามหาหนังสือที่ฉันมี
        $myBooks = Book::where('user_id', $userId)
            ->where('status', 'available')
            ->get();
        $otherWanted = WantedBook::where('user_id', '!=', $userId)
            ->with('user')
            ->get();

        $matchingDemand = [];
        $seenDemand = [];
        foreach ($myBooks as $book) {
            foreach ($otherWanted as $wanted) {
                if (self::isMatch($book->title, $book->author, $wanted->title, $wanted->author)) {
                    $key = $book->id . '_' . $wanted->id;
                    if (!isset($seenDemand[$key])) {
                        $seenDemand[$key] = true;
                        $matchingDemand[] = [
                            'my_book' => $book,
                            'wanted' => $wanted,
                            'seeker' => $wanted->user,
                        ];
                    }
                }
            }
        }

        return [
            'wishlist_matches' => $matchingWishlist,
            'demand_matches' => $matchingDemand,
        ];
    }

    /**
     * ดึงสรุปจำนวน Match สำหรับผู้ใช้
     */
    public static function getMatchCounts(User $user): array
    {
        $twoWay = self::getTwoWayMatches($user);
        $threeWay = self::getThreeWayMatches($user);
        $oneWay = self::getOneWayMatches($user);

        return [
            'two_way' => count($twoWay),
            'three_way' => count($threeWay),
            'one_way_wishlist' => count($oneWay['wishlist_matches']),
            'one_way_demand' => count($oneWay['demand_matches']),
            'total' => count($twoWay) + count($threeWay),
        ];
    }
}
