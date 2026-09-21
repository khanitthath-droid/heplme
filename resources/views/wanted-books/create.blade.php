<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                <span>➕</span>
                <span>เพิ่มหนังสือที่ฉันต้องการ</span>
            </h2>

            <a href="{{ route('wanted-books.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900">
                <span>←</span> <span>กลับ</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
                
                <div class="mb-6 pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">
                        ระบุหนังสือที่คุณอยากได้
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        ระบบจะทำการตรวจสอบกับคลังหนังสือของสมาชิกคนอื่นเพื่อหาคู่ที่ตรงกัน
                    </p>
                </div>

                <form method="POST" action="{{ route('wanted-books.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            ชื่อหนังสือที่ต้องการ <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="เช่น คิดแบบยิว ทำแบบญี่ปุ่น, Atomic Habits"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                        >
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Author --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            ผู้แต่ง / ผู้เขียน (ถ้าทราบ)
                        </label>
                        <input
                            type="text"
                            name="author"
                            value="{{ old('author') }}"
                            placeholder="เช่น James Clear"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                        >
                        @error('author')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Image Upload --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            รูปตัวอย่างหนังสือ (ไม่บังคับ)
                        </label>
                        <div class="border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-2xl p-4 text-center transition-colors">
                            <input
                                type="file"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer"
                            >
                            <p class="text-[11px] text-slate-400 mt-2">
                                รูปภาพปกตัวอย่าง JPG, PNG, WEBP ขนาดไม่เกิน 2MB
                            </p>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                            รายละเอียดเพิ่มเติมที่ต้องการ (เช่น ฉบับพิมพ์, สภาพที่รับได้)
                        </label>
                        <textarea
                            name="description"
                            rows="4"
                            placeholder="เช่น ขอสภาพอ่านได้ ไม่ขาด หรือรับฉบับพิมพ์ปี 2020 เป็นต้น..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Submit Toolbar --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('wanted-books.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-sm transition-colors">
                            ยกเลิก
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-sm hover:shadow active:scale-95 transition-all">
                            💾 บันทึกหนังสือที่ตามหา
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>