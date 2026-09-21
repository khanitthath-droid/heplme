<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชี Admin ที่กำลังใช้งานอยู่ได้');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }
}