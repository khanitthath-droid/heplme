<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>📚</span>
                    <span>หนังสือของฉัน (My Books)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    จัดการรายการหนังสือที่คุณครอบครองและเปิดให้สมาชิกคนอื่นแลกเปลี่ยน
                </p>
            </div>

            <a href="{{ route('books.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold shadow-sm hover:shadow-md active:scale-95 transition-all self-start sm:self-auto">
                <span>➕</span>
                <span>เพิ่มหนังสือเล่มใหม่</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Alert --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🎉</span>
                        <div>
                            <p class="font-bold text-sm">สำเร็จ!</p>
                            <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-lg p-1">✕</button>
                </div>
            @endif

            {{-- Search & Filter Bar --}}
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <form method="GET" action="{{ route('books.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            🔍
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ค้นหาชื่อหนังสือ, ผู้แต่ง หรือหมวดหมู่..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder:text-slate-400"
                        >
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button
                            type="submit"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs sm:text-sm font-semibold transition-all">
                            ค้นหา
                        </button>

                        @if(request('search'))
                            <a href="{{ route('books.index') }}" 
                               class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs sm:text-sm font-medium transition-all">
                                ล้างค้นหา
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Books Grid --}}
            @if($books->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($books as $book)
                        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-lg transition-all duration-200 flex flex-col overflow-hidden group">
                            
                            {{-- Standard Aspect Ratio Book Cover --}}
                            <div class="relative aspect-[3/4] bg-slate-100 overflow-hidden">
                                @if($book->image)
                                    <img src="{{ asset('storage/' . $book->image) }}"
                                         alt="{{ $book->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-slate-100 text-slate-400">
                                        <span class="text-4xl">📚</span>
                                        <span class="text-xs text-slate-400 mt-2 font-medium">ไม่มีรูปปก</span>
                                    </div>
                                @endif

                                {{-- Status Pill on Cover --}}
                                <div class="absolute top-3 right-3">
                                    @if($book->status === 'available')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/90 text-white backdrop-blur-xs shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            พร้อมแลก
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-600/90 text-white backdrop-blur-xs">
                                            แลกแล้ว
                                        </span>
                                    @endif
                                </div>

                                {{-- Condition Pill --}}
                                @if($book->condition)
                                    <div class="absolute top-3 left-3">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/90 text-slate-700 backdrop-blur-xs border border-slate-200/50 shadow-xs">
                                            สภาพ: {{ $book->condition }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Book Details Body --}}
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    @if($book->category)
                                        <span class="inline-block text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md mb-1.5">
                                            {{ $book->category }}
                                        </span>
                                    @endif

                                    <h3 class="font-bold text-sm sm:text-base text-slate-800 line-clamp-1 leading-snug" title="{{ $book->title }}">
                                        {{ $book->title }}
                                    </h3>

                                    <p class="text-xs text-slate-500 truncate mt-1">
                                        ผู้แต่ง: {{ $book->author ?? '-' }}
                                    </p>

                                    @if($book->description)
                                        <p class="text-xs text-slate-400 line-clamp-2 mt-2 leading-relaxed">
                                            {{ $book->description }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Action Buttons Toolbar --}}
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-1.5">
                                    <a href="{{ route('books.show', $book) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                                        <span>👁️</span> <span>ดู</span>
                                    </a>

                                    <a href="{{ route('books.edit', $book) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-xl text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">
                                        <span>✏️</span> <span>แก้ไข</span>
                                    </a>

                                    <form method="POST" action="{{ route('books.destroy', $book) }}"
                                          onsubmit="return confirm('ยืนยันว่าต้องการลบหนังสือเล่มนี้หรือไม่?');"
                                          class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                            <span>🗑️</span> <span>ลบ</span>
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-500 shadow-xs">
                    <div class="text-5xl mb-3">📚</div>
                    @if(request('search'))
                        <h3 class="font-bold text-base text-slate-700">ไม่พบหนังสือที่ตรงกับคำค้นหา "{{ request('search') }}"</h3>
                        <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 font-semibold mt-3 hover:underline">
                            ← แสดงหนังสือทั้งหมด
                        </a>
                    @else
                        <h3 class="font-bold text-base text-slate-700">คุณยังไม่มีหนังสือในคลัง</h3>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">เริ่มต้นลงประกาศหนังสือเล่มแรกเพื่อค้นหาคู่แลกเปลี่ยน</p>
                        <a href="{{ route('books.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition-all">
                            <span>➕</span> <span>เพิ่มหนังสือเล่มแรก</span>
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>