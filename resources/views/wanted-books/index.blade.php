<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>🔎</span>
                    <span>หนังสือที่ฉันต้องการ (Wanted Books)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    รายการหนังสือที่คุณกำลังตามหา ระบบจะนำไปจับคู่กับสมาชิกที่มีหนังสือเล่มนี้โดยอัตโนมัติ
                </p>
            </div>

            <a href="{{ route('wanted-books.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs sm:text-sm font-semibold shadow-sm hover:shadow-md active:scale-95 transition-all self-start sm:self-auto">
                <span>➕</span>
                <span>เพิ่มหนังสือที่ต้องการ</span>
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

            {{-- Information Banner --}}
            <div class="bg-amber-50/60 border border-amber-200/80 rounded-2xl p-4 sm:p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shrink-0">
                        💡
                    </div>
                    <div>
                        <h4 class="font-bold text-xs sm:text-sm text-slate-800">
                            เคล็ดลับการจับคู่หนังสือ
                        </h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            ยิ่งใส่ชื่อหนังสือหรือผู้แต่งที่ถูกต้อง ระบบจะยิ่งสามารถตรวจพบคู่แลกเปลี่ยนในหน้า "หนังสือที่ตรงกัน" ได้รวดเร็วขึ้น
                        </p>
                    </div>
                </div>

                <a href="{{ route('matching.index') }}" class="hidden sm:inline-flex items-center gap-1 text-xs font-bold text-amber-700 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 px-3.5 py-2 rounded-xl transition-all shrink-0">
                    <span>🎯 ตรวจสอบคู่จับคู่</span> <span>→</span>
                </a>
            </div>

            {{-- Wanted Books Grid --}}
            @if($wantedBooks->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($wantedBooks as $wantedBook)
                        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-lg transition-all duration-200 flex flex-col overflow-hidden group">
                            
                            {{-- Standard Aspect Ratio Book Cover --}}
                            <div class="relative aspect-[3/4] bg-slate-100 overflow-hidden">
                                @if($wantedBook->image)
                                    <img src="{{ asset('storage/' . $wantedBook->image) }}"
                                         alt="{{ $wantedBook->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-amber-50 to-slate-100 text-slate-400">
                                        <span class="text-4xl">🔎</span>
                                        <span class="text-xs text-slate-400 mt-2 font-medium">ไม่มีรูปภาพ</span>
                                    </div>
                                @endif

                                {{-- Wanted Badge --}}
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/95 text-white backdrop-blur-xs shadow-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        กำลังตามหา
                                    </span>
                                </div>
                            </div>

                            {{-- Details Body --}}
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="font-bold text-sm sm:text-base text-slate-800 line-clamp-1 leading-snug" title="{{ $wantedBook->title }}">
                                        {{ $wantedBook->title }}
                                    </h3>

                                    <p class="text-xs text-slate-500 truncate mt-1">
                                        ผู้แต่ง: {{ $wantedBook->author ?? 'ไม่ระบุ' }}
                                    </p>

                                    @if($wantedBook->description)
                                        <p class="text-xs text-slate-400 line-clamp-2 mt-2 leading-relaxed">
                                            {{ $wantedBook->description }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Action Bar --}}
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                    <span class="text-[11px] text-slate-400">
                                        เพิ่มเมื่อ {{ $wantedBook->created_at ? $wantedBook->created_at->format('d/m/Y') : '-' }}
                                    </span>

                                    <form method="POST" action="{{ route('wanted-books.destroy', $wantedBook) }}"
                                          onsubmit="return confirm('ยืนยันว่าต้องการลบรายการที่ตามหานี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                            <span>🗑️</span> <span>ลบออก</span>
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-500 shadow-xs">
                    <div class="text-5xl mb-3">🔎</div>
                    <h3 class="font-bold text-base text-slate-700">คุณยังไม่ได้เพิ่มหนังสือที่ต้องการ</h3>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">
                        บอกเราว่าคุณกำลังมองหาหนังสือเล่มไหน แล้วเราจะช่วยค้นหาคู่แลกเปลี่ยนให้คุณทันที
                    </p>
                    <a href="{{ route('wanted-books.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-semibold shadow-sm transition-all">
                        <span>➕</span> <span>เพิ่มหนังสือที่ต้องการเล่มแรก</span>
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>