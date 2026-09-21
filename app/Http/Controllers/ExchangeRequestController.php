<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRequest;
use App\Models\TradeRing;
use App\Models\Book;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExchangeRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // คำขอที่ได้รับ
        $receivedRequests = ExchangeRequest::where('receiver_id', $user->id)
            ->with([
                'requester:id,name,avatar',
                'offeredBook',
                'requestedBook'
            ])
            ->latest()
            ->get();

        // คำขอที่ส่ง
        $sentRequests = ExchangeRequest::where('requester_id', $user->id)
            ->with([
                'receiver:id,name,avatar',
                'offeredBook',
                'requestedBook'
            ])
            ->latest()
            ->get();

        // คำขอวงจร 3 ฝ่าย (Trade Rings) ที่เกี่ยวข้องกับผู้ใช้
        $tradeRings = TradeRing::where(function ($q) use ($user) {
            $q->where('user1_id', $user->id)
              ->orWhere('user2_id', $user->id)
              ->orWhere('user3_id', $user->id);
        })
        ->with([
            'initiator:id,name,avatar',
            'user1:id,name,avatar,phone,line_id,exchange_area',
            'book1',
            'user2:id,name,avatar,phone,line_id,exchange_area',
            'book2',
            'user3:id,name,avatar,phone,line_id,exchange_area',
            'book3'
        ])
        ->latest()
        ->get();

        // โหลดข้อมูลติดต่อเฉพาะคำขอที่ได้รับการยอมรับแล้ว
        $acceptedRequests = $receivedRequests
            ->where('status', 'accepted')
            ->concat(
                $sentRequests->where('status', 'accepted')
            );

        $userIds = $acceptedRequests
            ->flatMap(function ($request) {
                return [
                    $request->requester_id,
                    $request->receiver_id,
                ];
            })
            ->unique();

        $exchangeContacts = User::whereIn('id', $userIds)
            ->get([
                'id',
                'phone',
                'line_id',
                'exchange_area',
            ])
            ->keyBy('id');

        return view('exchange-requests.index', compact(
            'receivedRequests',
            'sentRequests',
            'tradeRings',
            'exchangeContacts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'offered_book_id' => 'required|exists:books,id',
            'requested_book_id' => 'required|exists:books,id',
        ]);

        $authId = Auth::id();

        // 1. ป้องกันการส่งคำขอแลกเปลี่ยนให้ตัวเอง
        if ($request->receiver_id == $authId) {
            return back()->with('error', 'ไม่สามารถส่งคำขอแลกเปลี่ยนให้ตัวเองได้');
        }

        // 2. ตรวจสอบการเป็นเจ้าของหนังสือทั้ง 2 เล่ม
        $offeredBook = Book::where('id', $request->offered_book_id)
            ->where('user_id', $authId)
            ->first();

        if (!$offeredBook) {
            return back()->with('error', 'คุณไม่ใช่เจ้าของหนังสือที่นำมาเสนอแลกเปลี่ยน หรือไม่พบข้อมูลหนังสือ');
        }

        $requestedBook = Book::where('id', $request->requested_book_id)
            ->where('user_id', $request->receiver_id)
            ->first();

        if (!$requestedBook) {
            return back()->with('error', 'หนังสือที่คุณต้องการแลกเปลี่ยนไม่มีอยู่ หรือไม่ได้เป็นของสมาชิกท่านนี้');
        }

        // 3. ตรวจสอบสถานะความพร้อมแลกเปลี่ยน (ป้องกันการส่งคำขอหนังสือที่แลกไปแล้ว)
        if ($offeredBook->status !== 'available') {
            return back()->with('error', 'หนังสือของคุณเล่มนี้ได้รับการแลกเปลี่ยนไปแล้ว ไม่สามารถนำมาเสนอแลกได้อีก');
        }

        if ($requestedBook->status !== 'available') {
            return back()->with('error', 'หนังสือที่คุณต้องการได้รับการแลกเปลี่ยนไปแล้ว');
        }

        // 4. ป้องกันการส่งคำขอซ้ำซ้อน (ทั้งขาไปและขากลับ)
        // 4.1 ตัวเราเคยส่งคำขอคู่นี้ไปแล้วและยังรอดำเนินการ
        $alreadySentPending = ExchangeRequest::where('requester_id', $authId)
            ->where('receiver_id', $request->receiver_id)
            ->where('offered_book_id', $request->offered_book_id)
            ->where('requested_book_id', $request->requested_book_id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadySentPending) {
            return back()->with('error', 'คุณได้ส่งคำขอแลกเปลี่ยนคู่นี้ไปแล้ว และกำลังรอการตอบรับ');
        }

        // 4.2 อีกฝ่ายได้ส่งคำขอแลกเปลี่ยนคู่นี้มาหาเราอยู่แล้ว (ส่งสวนทางกัน)
        $alreadyReceivedPending = ExchangeRequest::where('requester_id', $request->receiver_id)
            ->where('receiver_id', $authId)
            ->where('offered_book_id', $request->requested_book_id)
            ->where('requested_book_id', $request->offered_book_id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReceivedPending) {
            return back()->with('error', 'อีกฝ่ายได้ส่งคำขอแลกเปลี่ยนคู่นี้มาถึงคุณแล้ว คุณสามารถไปกดยอมรับได้ในหน้ารายการคำขอ');
        }

        // 4.3 หนังสือคู่นี้เคยแลกเปลี่ยนสำเร็จไปแล้ว
        $alreadyAccepted = ExchangeRequest::where(function ($q) use ($authId, $request) {
            $q->where(function ($sub) use ($authId, $request) {
                $sub->where('requester_id', $authId)
                    ->where('receiver_id', $request->receiver_id)
                    ->where('offered_book_id', $request->offered_book_id)
                    ->where('requested_book_id', $request->requested_book_id);
            })->orWhere(function ($sub) use ($authId, $request) {
                $sub->where('requester_id', $request->receiver_id)
                    ->where('receiver_id', $authId)
                    ->where('offered_book_id', $request->requested_book_id)
                    ->where('requested_book_id', $request->offered_book_id);
            });
        })->where('status', 'accepted')->exists();

        if ($alreadyAccepted) {
            return back()->with('error', 'หนังสือคู่นี้ได้ทำการแลกเปลี่ยนสำเร็จไปแล้ว');
        }

        // 5. บันทึกคำขอแลกเปลี่ยน
        ExchangeRequest::create([
            'requester_id' => $authId,
            'receiver_id' => $request->receiver_id,
            'offered_book_id' => $request->offered_book_id,
            'requested_book_id' => $request->requested_book_id,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('exchange-requests.index')
            ->with(
                'success',
                'ส่งคำขอแลกเปลี่ยนเรียบร้อยแล้ว'
            );
    }

    public function accept(ExchangeRequest $exchangeRequest)
    {
        if ($exchangeRequest->receiver_id !== Auth::id()) {
            abort(403);
        }

        if ($exchangeRequest->status !== 'pending') {
            return back()->with(
                'error',
                'คำขอนี้ได้รับการดำเนินการแล้ว'
            );
        }

        $offeredBook = $exchangeRequest->offeredBook;
        $requestedBook = $exchangeRequest->requestedBook;

        // ตรวจสอบว่าหนังสือทั้งสองเล่มยังคงอยู่ในสถานะ available หรือไม่
        if (!$offeredBook || $offeredBook->status !== 'available' || !$requestedBook || $requestedBook->status !== 'available') {
            $exchangeRequest->update(['status' => 'rejected']);
            return back()->with(
                'error',
                'ไม่สามารถยอมรับคำขอได้ เนื่องจากหนังสือเล่มใดเล่มหนึ่งถูกแลกเปลี่ยนไปแล้ว'
            );
        }

        // ดำเนินการแลกเปลี่ยนด้วย Transaction
        DB::transaction(function () use ($exchangeRequest, $offeredBook, $requestedBook) {
            // 1. อัปเดตสถานะคำขอเป็น accepted
            $exchangeRequest->update([
                'status' => 'accepted',
            ]);

            // 2. อัปเดตสถานะหนังสือทั้งสองเล่มเป็น exchanged (แลกเปลี่ยนแล้ว)
            $offeredBook->update(['status' => 'exchanged']);
            $requestedBook->update(['status' => 'exchanged']);

            // 3. ปฏิเสธ/ยกเลิกคำขออื่นที่ค้างอยู่ (pending) ซึ่งเกี่ยวข้องกับหนังสือ 2 เล่มนี้โดยอัตโนมัติ
            ExchangeRequest::where('id', '!=', $exchangeRequest->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($offeredBook, $requestedBook) {
                    $q->where('offered_book_id', $offeredBook->id)
                      ->orWhere('requested_book_id', $offeredBook->id)
                      ->orWhere('offered_book_id', $requestedBook->id)
                      ->orWhere('requested_book_id', $requestedBook->id);
                })
                ->update(['status' => 'rejected']);

            // 4. สร้างหรือเชื่อมต่อห้องแชตอัตโนมัติ พร้อมส่งข้อความระบบ
            $chatRoom = ChatRoom::firstOrCreate(
                ['exchange_request_id' => $exchangeRequest->id],
                [
                    'user1_id' => $exchangeRequest->requester_id,
                    'user2_id' => $exchangeRequest->receiver_id,
                    'last_message_at' => now(),
                ]
            );

            ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => Auth::id(),
                'type' => 'system',
                'message' => '🤝 ยอมรับคำขอแลกเปลี่ยนแล้ว! ทั้งสองฝ่ายสามารถเริ่มพูดคุย ตกลงสถานที่นัดรับ หรือแจ้งเลขพัสดุผ่านแชตนี้ได้ทันที',
                'is_read' => false,
            ]);
        });

        return back()->with(
            'success',
            'ยอมรับคำขอแลกเปลี่ยนเรียบร้อยแล้ว หนังสือทั้งสองเล่มถูกปรับสถานะเป็นแลกเปลี่ยนแล้ว คุณสามารถเปิดห้องแชตเพื่อประสานงานได้ทันที'
        );
    }

    public function reject(ExchangeRequest $exchangeRequest)
    {
        if ($exchangeRequest->receiver_id !== Auth::id()) {
            abort(403);
        }

        if ($exchangeRequest->status !== 'pending') {
            return back()->with(
                'error',
                'คำขอนี้ได้รับการดำเนินการแล้ว'
            );
        }

        $exchangeRequest->update([
            'status' => 'rejected',
        ]);

        return back()->with(
            'success',
            'ปฏิเสธคำขอแลกเปลี่ยนแล้ว'
        );
    }

    public function confirmReceived(ExchangeRequest $exchangeRequest)
    {
        $userId = Auth::id();

        if ($exchangeRequest->requester_id !== $userId && $exchangeRequest->receiver_id !== $userId) {
            abort(403);
        }

        if ($exchangeRequest->status !== 'accepted' && $exchangeRequest->status !== 'completed') {
            return back()->with('error', 'คำขอนี้ยังไม่ได้รับการยอมรับแลกเปลี่ยน');
        }

        if ($exchangeRequest->requester_id === $userId) {
            $exchangeRequest->update(['requester_confirmed_at' => now()]);
        } else {
            $exchangeRequest->update(['receiver_confirmed_at' => now()]);
        }

        $exchangeRequest->refresh();

        $chatRoom = ChatRoom::where('exchange_request_id', $exchangeRequest->id)->first();

        // ตรวจสอบว่าทั้งสองฝ่ายกดยืนยันแล้วหรือไม่
        if ($exchangeRequest->requester_confirmed_at && $exchangeRequest->receiver_confirmed_at) {
            $exchangeRequest->update(['status' => 'completed']);

            if ($chatRoom) {
                ChatMessage::create([
                    'chat_room_id' => $chatRoom->id,
                    'sender_id' => $userId,
                    'type' => 'system',
                    'message' => '🎉 ยอดเยี่ยม! ทั้งสองฝ่ายกดยืนยันการรับมอบหนังสือเรียบร้อยแล้ว รายการแลกเปลี่ยนนี้เสร็จสมบูรณ์ 100%',
                    'is_read' => false,
                ]);
            }

            return back()->with('success', 'ยืนยันรับหนังสือเรียบร้อยแล้ว! รายการแลกเปลี่ยนนี้เสร็จสมบูรณ์');
        }

        if ($chatRoom) {
            ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => $userId,
                'type' => 'system',
                'message' => '📦 ' . Auth::user()->name . ' ได้กดยืนยันว่าได้รับหนังสือแล้ว (กำลังรอการยืนยันจากอีกฝ่าย)',
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'บันทึกการยืนยันรับหนังสือแล้ว รออีกฝ่ายกดยืนยัน');
    }
}