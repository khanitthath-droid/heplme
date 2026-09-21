<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'blocked_user_id' => 'required|exists:users,id',
        ]);

        $authId = Auth::id();
        if ($request->blocked_user_id == $authId) {
            return back()->with('error', 'ไม่สามารถบล็อกตัวเองได้');
        }

        UserBlock::firstOrCreate([
            'user_id' => $authId,
            'blocked_user_id' => $request->blocked_user_id,
        ]);

        return back()->with('success', 'บล็อกผู้ใช้เรียบร้อยแล้ว');
    }

    public function destroy(User $user)
    {
        UserBlock::where('user_id', Auth::id())
            ->where('blocked_user_id', $user->id)
            ->delete();

        return back()->with('success', 'ปลดบล็อกผู้ใช้เรียบร้อยแล้ว');
    }
}
