<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Report::with([
            'reporter:id,name,email,avatar',
            'reportedUser:id,name,email,avatar',
            'chatRoom',
            'chatMessage',
        ])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(15);
        $pendingCount = Report::where('status', 'pending')->count();
        $resolvedCount = Report::where('status', 'resolved')->count();
        $dismissedCount = Report::where('status', 'dismissed')->count();

        return view('admin.reports.index', compact(
            'reports',
            'status',
            'pendingCount',
            'resolvedCount',
            'dismissedCount'
        ));
    }

    public function update(Report $report, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'อัปเดตสถานะการตรวจสอบเรียบร้อยแล้ว');
    }

    public function deleteMessage(ChatMessage $chatMessage)
    {
        $chatMessage->update([
            'deleted_by_sender' => true,
            'message' => 'ข้อความนี้ถูกระงับหรือลบโดยผู้ดูแลระบบเนื่องจากละเมิดกฎการใช้งาน',
            'image_path' => null,
        ]);

        return back()->with('success', 'ลบข้อความที่ไม่เหมาะสมเรียบร้อยแล้ว');
    }
}
