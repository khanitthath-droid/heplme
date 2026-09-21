<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'chat_room_id' => 'nullable|exists:chat_rooms,id',
            'chat_message_id' => 'nullable|exists:chat_messages,id',
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        $authId = Auth::id();
        if ($request->reported_user_id == $authId) {
            return back()->with('error', 'ไม่สามารถรายงานตัวเองได้');
        }

        Report::create([
            'reporter_id' => $authId,
            'reported_user_id' => $request->reported_user_id,
            'chat_room_id' => $request->chat_room_id,
            'chat_message_id' => $request->chat_message_id,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return back()->with('success', 'ส่งรายงานไปยังผู้ดูแลระบบเรียบร้อยแล้ว เราจะดำเนินการตรวจสอบโดยเร็ว');
    }
}
