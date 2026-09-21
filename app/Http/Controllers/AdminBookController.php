<?php

namespace App\Http\Controllers;

use App\Models\Book;

class AdminBookController extends Controller
{
    public function index()
    {
        $books = Book::with('user')
            ->latest()
            ->get();

        return view('admin.books.index', compact('books'));
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'ลบหนังสือเรียบร้อยแล้ว');
    }
}