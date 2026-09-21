<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                <span>📖</span>
                <span>รายละเอียดหนังสือ</span>
            </h2>

            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900">
                <span>←</span> <span>กลับไปหน้ารายการ</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col md:flex-row">
                
                {{-- Book Cover Preview --}}
                <div class="w-full md:w-80 bg-slate-100 flex items-center justify-center p-6 shrink-0">
                    <div class="w-full max-w-[240px] aspect-[3/4] bg-white rounded-2xl overflow-hidden shadow-md">
                        @if($book->image)
                            <img src="{{ asset('storage/' . $book->image) }}" 
                                 alt="{{ $book->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-slate-100 text-slate-400">
                                <span class="text-5xl">📚</span>
                                <span class="text-xs text-slate-400 mt-2 font-medium">ไม่มีรูปภาพปก</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Book Information --}}
                <div class="p-6 sm:p-8 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            @if($book->category)
                                <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-semibold">
                                    {{ $book->category }}
                                </span>
                            @endif

                            @if($book->status === 'available')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    พร้อมแลกเปลี่ยน
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    แลกเปลี่ยนแล้ว
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 leading-tight">
                            {{ $book->title }}
                        </h1>

                        <p class="text-sm text-slate-500 mt-1">
                            ผู้แต่ง: <span class="font-semibold text-slate-700">{{ $book->author ?? 'ไม่ระบุ' }}</span>
                        </p>

                        @if($book->condition)
                            <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 inline-block">
                                <span class="text-xs text-slate-500">สภาพหนังสือ:</span>
                                <span class="text-xs font-bold text-slate-800 ml-1">{{ $book->condition }}</span>
                            </div>
                        @endif

                        <div class="mt-6">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                                รายละเอียดเกี่ยวกับหนังสือ
                            </h4>
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                                {{ $book->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex items-center gap-3">
                        <a href="{{ route('books.edit', $book) }}" 
                           class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm shadow-sm transition-all">
                            <span>✏️</span> <span>แก้ไขข้อมูล</span>
                        </a>

                        <form method="POST" action="{{ route('books.destroy', $book) }}"
                              onsubmit="return confirm('ยืนยันว่าต้องการลบหนังสือเล่มนี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-sm border border-red-200 transition-all">
                                <span>🗑️</span> <span>ลบหนังสือ</span>
                            </button>
                        </form>

                        <a href="{{ route('books.index') }}" 
                           class="inline-flex items-center gap-1 px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-medium text-sm transition-all ml-auto">
                            <span>← กลับ</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>