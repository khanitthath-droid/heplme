<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->get();

        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('books', 'public');
        }

        Book::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'author' => $request->author,
            'category' => $request->category,
            'description' => $request->description,
            'condition' => $request->condition,
            'image' => $imagePath,
            'status' => 'available',
        ]);

        return redirect()
            ->route('books.index')
            ->with('success', 'เพิ่มหนังสือเรียบร้อยแล้ว');
    }

    public function show(Book $book)
    {
        abort_if($book->user_id !== auth()->id(), 403);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        abort_if($book->user_id !== auth()->id(), 403);

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        abort_if($book->user_id !== auth()->id(), 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'author' => $request->author,
            'category' => $request->category,
            'description' => $request->description,
            'condition' => $request->condition,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('books', 'public');
        }

        $book->update($data);

        return redirect()
            ->route('books.index')
            ->with('success', 'แก้ไขหนังสือเรียบร้อยแล้ว');
    }

    public function destroy(Book $book)
    {
        abort_if($book->user_id !== auth()->id(), 403);

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'ลบหนังสือเรียบร้อยแล้ว');
    }
}