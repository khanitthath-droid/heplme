<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                <span>✏️</span>
                <span>แก้ไขข้อมูลหนังสือ</span>
            </h2>

            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900">
                <span>←</span> <span>กลับ</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
                
                <div class="mb-6 pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">
                        แก้ไขข้อมูล: {{ $book->title }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        อัปเดตข้อมูลให้เป็นปัจจุบันเพื่อความสะดวกในการแลกเปลี่ยน
                    </p>
                </div>

                <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            ชื่อหนังสือ <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $book->title) }}"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                        >
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Author & Category --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                                ผู้แต่ง / นักเขียน
                            </label>
                            <input
                                type="text"
                                name="author"
                                value="{{ old('author', $book->author) }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                            >
                        </div>

                        <div>
                            <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                                หมวดหมู่
                            </label>
                            <input
                                type="text"
                                name="category"
                                value="{{ old('category', $book->category) }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                            >
                        </div>
                    </div>

                    {{-- Condition --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            สภาพหนังสือ
                        </label>
                        <select
                            name="condition"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all bg-white"
                        >
                            <option value="">-- เลือกสภาพหนังสือ --</option>
                            <option value="ใหม่ (100%)" {{ old('condition', $book->condition) == 'ใหม่ (100%)' ? 'selected' : '' }}>ใหม่ (100%) - ยังไม่เคยเปิดอ่าน</option>
                            <option value="ดีมาก (90-95%)" {{ old('condition', $book->condition) == 'ดีมาก (90-95%)' ? 'selected' : '' }}>ดีมาก (90-95%) - ไม่มีรอยยับ</option>
                            <option value="ดี (80-85%)" {{ old('condition', $book->condition) == 'ดี (80-85%)' ? 'selected' : '' }}>ดี (80-85%) - มีร่องรอยการอ่านเล็กน้อย</option>
                            <option value="พอใช้ (70%)" {{ old('condition', $book->condition) == 'พอใช้ (70%)' ? 'selected' : '' }}>พอใช้ (70%) - มีรอยพับหรือไฮไลท์</option>
                        </select>
                    </div>

                    {{-- Current Image & Upload --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            รูปภาพปกหนังสือ
                        </label>
                        
                        @if($book->image)
                            <div class="mb-3 flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <img src="{{ asset('storage/' . $book->image) }}" 
                                     alt="Current Cover" 
                                     class="w-16 h-20 object-cover rounded-lg shadow-xs">
                                <div>
                                    <p class="text-xs font-semibold text-slate-700">รูปภาพปกปัจจุบัน</p>
                                    <p class="text-[11px] text-slate-400">เลือกไฟล์ใหม่ด้านล่างหากต้องการเปลี่ยน</p>
                                </div>
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-2xl p-4 text-center transition-colors">
                            <input
                                type="file"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                            >
                            <p class="text-[11px] text-slate-400 mt-2">
                                รองรับไฟล์รูปภาพ JPG, PNG, WEBP ขนาดไม่เกิน 2MB
                            </p>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            รายละเอียดเพิ่มเติม / เรื่องย่อ
                        </label>
                        <textarea
                            name="description"
                            rows="4"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                        >{{ old('description', $book->description) }}</textarea>
                    </div>

                    {{-- Submit Toolbar --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('books.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-sm transition-colors">
                            ยกเลิก
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-sm hover:shadow active:scale-95 transition-all">
                            💾 บันทึกการแก้ไข
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>