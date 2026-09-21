<?php

namespace App\Http\Controllers;

use App\Models\WantedBook;

class AdminWantedBookController extends Controller
{
    public function index()
    {
        $wantedBooks = WantedBook::with('user')
            ->latest()
            ->get();

        return view('admin.wanted-books.index', compact('wantedBooks'));
    }

    public function destroy(WantedBook $wantedBook)
    {
        $wantedBook->delete();

        return redirect()
            ->route('admin.wanted-books.index')
            ->with('success', 'ลบหนังสือที่ต้องการเรียบร้อยแล้ว');
    }
}