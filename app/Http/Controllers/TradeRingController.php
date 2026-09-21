<?php

namespace App\Http\Controllers;

use App\Models\TradeRing;
use App\Models\Book;
use App\Models\ExchangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TradeRingController extends Controller
{
    /**
     * เริ่มต้นส่งข้อเสนอแลกเปลี่ยนแบบวงจร 3 ฝ่าย
     */
    public function store(Request $request)
    {
        $request->validate([
            'user1_id' => 'required|exists:users,id',
            'book1_id' => 'required|exists:books,id',
            'user2_id' => 'required|exists:users,id',
            'book2_id' => 'required|exists:books,id',
            'user3_id' => 'required|exists:users,id',
            'book3_id' => 'required|exists:books,id',
        ]);

        $authId = Auth::id();

        // 1. ตรวจสอบว่าผู้ใช้งานปัจจุบันเป็น 1 ใน 3 คนในวงจร
        if (!in_array($authId, [$request->user1_id, $request->user2_id, $request->user3_id])) {
            return back()->with('error', 'คุณต้องเป็นหนึ่งในผู้เข้าร่วมวงจรแลกเปลี่ยน 3 ฝ่ายนี้');
        }

        // 2. ตรวจสอบความถูกต้องของหนังสือและเจ้าของทั้ง 3 คน
        $book1 = Book::where('id', $request->book1_id)->where('user_id', $request->user1_id)->first();
        $book2 = Book::where('id', $request->book2_id)->where('user_id', $request->user2_id)->first();
        $book3 = Book::where('id', $request->book3_id)->where('user_id', $request->user3_id)->first();

        if (!$book1 || !$book2 || !$book3) {
            return back()->with('error', 'ข้อมูลหนังสือหรือเจ้าของหนังสือไม่ถูกต้อง');
        }

        // 3. ตรวจสอบสถานะความพร้อมแลกเปลี่ยนของหนังสือทั้ง 3 เล่ม
        if ($book1->status !== 'available' || $book2->status !== 'available' || $book3->status !== 'available') {
            return back()->with('error', 'ไม่สามารถสร้างข้อเสนอได้ เนื่องจากมีหนังสือบางเล่มถูกแลกเปลี่ยนไปแล้ว');
        }

        // 4. ตรวจสอบว่ามีคำขอวงจรชุดหนังสือ 3 เล่มนี้อยู่แล้วหรือไม่ (ป้องกันการส่งซ้ำ)
        $bookIds = [$book1->id, $book2->id, $book3->id];
        $existingRing = TradeRing::whereIn('status', ['pending', 'accepted'])
            ->where(function ($q) use ($bookIds) {
                $q->whereIn('book1_id', $bookIds)
                  ->whereIn('book2_id', $bookIds)
                  ->whereIn('book3_id', $bookIds);
            })
            ->first();

        if ($existingRing) {
            if ($existingRing->status === 'accepted') {
                return back()->with('error', 'วงจรแลกเปลี่ยนหนังสือชุดนี้ได้รับการแลกเปลี่ยนสำเร็จไปแล้ว');
            }
            return back()->with('error', 'มีข้อเสนอแลกเปลี่ยนวงจร 3 ฝ่ายสำหรับหนังสือชุดนี้อยู่ในระบบแล้ว');
        }

        // 5. กำหนดสถานะเริ่มต้น (ผู้สร้าง = 'accepted', อีก 2 ท่าน = 'pending')
        $u1Status = ($authId == $request->user1_id) ? 'accepted' : 'pending';
        $u2Status = ($authId == $request->user2_id) ? 'accepted' : 'pending';
        $u3Status = ($authId == $request->user3_id) ? 'accepted' : 'pending';

        TradeRing::create([
            'initiator_id' => $authId,
            'user1_id' => $request->user1_id,
            'book1_id' => $request->book1_id,
            'user1_status' => $u1Status,
            'user2_id' => $request->user2_id,
            'book2_id' => $request->book2_id,
            'user2_status' => $u2Status,
            'user3_id' => $request->user3_id,
            'book3_id' => $request->book3_id,
            'user3_status' => $u3Status,
            'status' => 'pending',
        ]);

        return redirect()->route('matching.index')->with(
            'success',
            'ส่งคำขอแลกเปลี่ยนวงจร 3 ฝ่ายเรียบร้อยแล้ว! กำลังรอการยืนยันจากสมาชิกอีก 2 ท่าน'
        );
    }

    /**
     * กดยืนยันยอมรับข้อเสนอแลกเปลี่ยนวงจร 3 ฝ่าย
     */
    public function accept(TradeRing $tradeRing)
    {
        $authId = Auth::id();

        if (!$tradeRing->isParticipant($authId)) {
            abort(403);
        }

        if ($tradeRing->status !== 'pending') {
            return back()->with('error', 'ข้อเสนอแลกเปลี่ยน 3 ฝ่ายนี้ได้รับการดำเนินการแล้ว');
        }

        // ตรวจสอบว่าหนังสือทั้ง 3 เล่มยังคงพร้อมแลกเปลี่ยน
        $b1 = $tradeRing->book1;
        $b2 = $tradeRing->book2;
        $b3 = $tradeRing->book3;

        if (!$b1 || $b1->status !== 'available' || !$b2 || $b2->status !== 'available' || !$b3 || $b3->status !== 'available') {
            $tradeRing->update(['status' => 'rejected']);
            return back()->with('error', 'ไม่สามารถยอมรับได้ เนื่องจากมีหนังสือบางเล่มในวงจรถูกแลกเปลี่ยนไปแล้ว');
        }

        // บันทึกการยอมรับของผู้ใช้ปัจจุบัน
        $tradeRing->setUserStatus($authId, 'accepted');

        // ถ้าทั้ง 3 ฝ่ายกดยืนยันครบ 3/3 คน
        if ($tradeRing->allAccepted()) {
            DB::transaction(function () use ($tradeRing, $b1, $b2, $b3) {
                // 1. อัปเดตสถานะของ TradeRing เป็น accepted
                $tradeRing->status = 'accepted';
                $tradeRing->save();

                // 2. ปรับสถานะหนังสือทั้ง 3 เล่มเป็น exchanged (แลกเปลี่ยนแล้ว)
                $b1->update(['status' => 'exchanged']);
                $b2->update(['status' => 'exchanged']);
                $b3->update(['status' => 'exchanged']);

                $bookIds = [$b1->id, $b2->id, $b3->id];

                // 3. ปฏิเสธคำขอ 2-Way อื่นๆ ที่ขอหนังสือ 3 เล่มนี้ค้างอยู่
                ExchangeRequest::where('status', 'pending')
                    ->where(function ($q) use ($bookIds) {
                        $q->whereIn('offered_book_id', $bookIds)
                          ->orWhereIn('requested_book_id', $bookIds);
                    })
                    ->update(['status' => 'rejected']);

                // 4. ปฏิเสธ TradeRing อื่นๆ ที่เกี่ยวข้องกับหนังสือ 3 เล่มนี้
                TradeRing::where('id', '!=', $tradeRing->id)
                    ->where('status', 'pending')
                    ->where(function ($q) use ($bookIds) {
                        $q->whereIn('book1_id', $bookIds)
                          ->orWhereIn('book2_id', $bookIds)
                          ->orWhereIn('book3_id', $bookIds);
                    })
                    ->update(['status' => 'rejected']);
            });

            return back()->with(
                'success',
                '🎉 ยินดีด้วย! ทั้ง 3 ฝ่ายกดยืนยันครบถ้วนแล้ว ระบบได้ทำการแลกเปลี่ยนและปลดล็อคข้อมูลติดต่อให้ครบทุกคนเรียบร้อยแล้ว'
            );
        }

        $tradeRing->save();

        return back()->with(
            'success',
            'คุณได้กดยืนยันการแลกเปลี่ยน 3 ฝ่ายแล้ว (ความคืบหน้า: ยืนยันแล้ว ' . $tradeRing->acceptedCount() . '/3 คน)'
        );
    }

    /**
     * ปฏิเสธข้อเสนอแลกเปลี่ยนวงจร 3 ฝ่าย
     */
    public function reject(TradeRing $tradeRing)
    {
        $authId = Auth::id();

        if (!$tradeRing->isParticipant($authId)) {
            abort(403);
        }

        if ($tradeRing->status !== 'pending') {
            return back()->with('error', 'ข้อเสนอนี้ได้รับการดำเนินการแล้ว');
        }

        $tradeRing->setUserStatus($authId, 'rejected');
        $tradeRing->status = 'rejected';
        $tradeRing->save();

        return back()->with(
            'success',
            'คุณได้ปฏิเสธข้อเสนอแลกเปลี่ยน 3 ฝ่ายนี้แล้ว'
        );
    }
}
