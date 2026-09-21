<?php

namespace App\Http\Controllers;

use App\Models\WantedBook;
use Illuminate\Http\Request;

class WantedBookController extends Controller
{
    public function index()
    {
        $wantedBooks = WantedBook::where('user_id', auth()->id())->get();

        return view('wanted-books.index', compact('wantedBooks'));
    }

    public function create()
    {
        return view('wanted-books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('wanted-books', 'public');
        }

        WantedBook::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('wanted-books.index')
            ->with('success', 'เพิ่มหนังสือที่ต้องการเรียบร้อยแล้ว');
    }

        public function destroy(WantedBook $wantedBook)
    {
        $wantedBook->delete();

        return redirect()
            ->route('wanted-books.index')
            ->with('success', 'ลบหนังสือที่ต้องการเรียบร้อยแล้ว');
    }
}