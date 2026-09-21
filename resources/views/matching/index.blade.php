<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 leading-tight flex items-center gap-2.5">
                    <span class="text-3xl">🎯</span>
                    <span>ระบบจับคู่หนังสืออัจฉริยะ (Smart Matching)</span>
                </h2>
                <p class="text-sm text-slate-600 font-medium mt-1">
                    ระบบจับคู่แลกเปลี่ยนอัตโนมัติ ทั้งแบบตรง 2 ทาง (2-Way) และแบบลูกโซ่หมุนเวียน 3 ทาง (3-Way Circular Ring)
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('books.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow transition-all">
                    <span>➕</span>
                    <span>ลงหนังสือเพิ่ม</span>
                </a>
                <a href="{{ route('wanted-books.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-amber-500 text-white hover:bg-amber-600 shadow-sm hover:shadow transition-all">
                    <span>🔎</span>
                    <span>ประกาศหาเพิ่ม</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $defaultTab = count($twoWayMatches) > 0 ? 'two_way' : (count($threeWayMatches) > 0 ? 'three_way' : (count($oneWayMatches['wishlist_matches']) > 0 ? 'one_way' : 'two_way'));
    @endphp

    <div class="py-8" x-data="{ activeTab: '{{ $defaultTab }}' }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-900 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🎉</span>
                        <div>
                            <p class="font-extrabold text-sm text-emerald-950">สำเร็จ!</p>
                            <p class="text-xs sm:text-sm text-emerald-800 font-medium mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-xl font-bold p-1">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 border-2 border-red-200 text-red-900 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">⚠️</span>
                        <div>
                            <p class="font-extrabold text-sm text-red-950">แจ้งเตือน</p>
                            <p class="text-xs sm:text-sm text-red-800 font-medium mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-600 hover:text-red-900 text-xl font-bold p-1">✕</button>
                </div>
            @endif

            {{-- Overall Statistics Summary Banner (High Contrast & Clear Typography) --}}
            <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-purple-400/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-10 -top-10 w-56 h-56 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2 max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/20 text-white border border-white/30 backdrop-blur-md text-xs font-extrabold tracking-wide">
                            <span>✨ ระบบจับคู่อัจฉริยะ</span>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                            ค้นพบโอกาสแลกเปลี่ยนทั้งหมด <span class="text-amber-300 underline decoration-amber-400 decoration-wavy">{{ $counts['total'] }}</span> รูปแบบ
                        </h3>
                        <p class="text-indigo-100 text-xs sm:text-sm font-medium leading-relaxed">
                            จับคู่ทั้งการแลกเปลี่ยนตรงแบบ 2 ฝ่าย และวงจรแลกเปลี่ยนหมุนเวียน 3 ฝ่าย ช่วยให้คุณได้หนังสือเล่มโปรดได้เร็วขึ้น
                        </p>
                    </div>

                    {{-- Clickable Quick Stat Cards --}}
                    <div class="grid grid-cols-3 gap-3 shrink-0">
                        <button 
                            type="button"
                            @click="activeTab = 'two_way'" 
                            :class="activeTab === 'two_way' ? 'ring-3 ring-white shadow-lg scale-105' : 'opacity-90 hover:opacity-100 hover:scale-102'"
                            class="p-3 sm:p-4 rounded-2xl bg-white text-slate-800 text-center transition-all duration-200 cursor-pointer shadow-md">
                            <div class="text-2xl sm:text-3xl font-black text-emerald-600">{{ count($twoWayMatches) }}</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">จับคู่ 2 ทาง</div>
                            <div class="text-[10px] text-slate-500 font-medium hidden sm:block">แลกเปลี่ยนตรง</div>
                        </button>

                        <button 
                            type="button"
                            @click="activeTab = 'three_way'" 
                            :class="activeTab === 'three_way' ? 'ring-3 ring-white shadow-lg scale-105' : 'opacity-90 hover:opacity-100 hover:scale-102'"
                            class="p-3 sm:p-4 rounded-2xl bg-white text-slate-800 text-center transition-all duration-200 cursor-pointer shadow-md">
                            <div class="text-2xl sm:text-3xl font-black text-purple-600">{{ count($threeWayMatches) }}</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">ลูกโซ่ 3 ทาง</div>
                            <div class="text-[10px] text-slate-500 font-medium hidden sm:block">วงจร 3 ฝ่าย</div>
                        </button>

                        <button 
                            type="button"
                            @click="activeTab = 'one_way'" 
                            :class="activeTab === 'one_way' ? 'ring-3 ring-white shadow-lg scale-105' : 'opacity-90 hover:opacity-100 hover:scale-102'"
                            class="p-3 sm:p-4 rounded-2xl bg-white text-slate-800 text-center transition-all duration-200 cursor-pointer shadow-md">
                            <div class="text-2xl sm:text-3xl font-black text-amber-600">{{ count($oneWayMatches['wishlist_matches']) }}</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">ตรงใจ 1 ทาง</div>
                            <div class="text-[10px] text-slate-500 font-medium hidden sm:block">ตาม Wishlist</div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabs Navigation Bar --}}
            <div class="bg-white p-2 rounded-2xl border-2 border-slate-200/90 shadow-sm flex items-center gap-2 overflow-x-auto">
                <button 
                    type="button"
                    @click="activeTab = 'two_way'"
                    :class="activeTab === 'two_way' ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 hover:bg-slate-100 font-bold'"
                    class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all duration-150 shrink-0">
                    <span class="text-base">🎯</span>
                    <span>จับคู่ตรง 2 ทาง (2-Way)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black"
                          :class="activeTab === 'two_way' ? 'bg-white text-indigo-700 shadow-xs' : 'bg-slate-200 text-slate-800'">
                        {{ count($twoWayMatches) }}
                    </span>
                </button>

                <button 
                    type="button"
                    @click="activeTab = 'three_way'"
                    :class="activeTab === 'three_way' ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 hover:bg-slate-100 font-bold'"
                    class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all duration-150 shrink-0">
                    <span class="text-base">🔄</span>
                    <span>จับคู่ลูกโซ่ 3 ทาง (3-Way Ring)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black"
                          :class="activeTab === 'three_way' ? 'bg-white text-indigo-700 shadow-xs' : 'bg-slate-200 text-slate-800'">
                        {{ count($threeWayMatches) }}
                    </span>
                </button>

                <button 
                    type="button"
                    @click="activeTab = 'one_way'"
                    :class="activeTab === 'one_way' ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 hover:bg-slate-100 font-bold'"
                    class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all duration-150 shrink-0">
                    <span class="text-base">💡</span>
                    <span>แนะนำความต้องการ (1-Way)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black"
                          :class="activeTab === 'one_way' ? 'bg-white text-indigo-700 shadow-xs' : 'bg-slate-200 text-slate-800'">
                        {{ count($oneWayMatches['wishlist_matches']) + count($oneWayMatches['demand_matches']) }}
                    </span>
                </button>
            </div>

            {{-- ==========================================
                TAB 1: 2-WAY DIRECT MATCHES
            =========================================== --}}
            <div x-show="activeTab === 'two_way'" x-transition class="space-y-6">
                @if (count($twoWayMatches) > 0)
                    <div class="space-y-6">
                        @foreach ($twoWayMatches as $match)
                            @php
                                $myBook = $match['my_book'];
                                $otherBook = $match['other_book'];
                                $otherUser = $match['other_user'];
                                $myWanted = $match['my_wanted'];
                                $otherWanted = $match['other_wanted'];
                                $existingReq = $match['existing_request'] ?? null;
                            @endphp

                            <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                                
                                {{-- Card Header --}}
                                <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 via-teal-50 to-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                            ✨ จับคู่ 2-Way Match สำเร็จ
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-700">
                                        <span class="font-bold text-slate-600">คู่แลกเปลี่ยน:</span>
                                        <span class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                            @if($otherUser && $otherUser->avatar)
                                                <img src="{{ asset('storage/' . $otherUser->avatar) }}" alt="{{ $otherUser->name }}" class="w-7 h-7 rounded-full object-cover ring-2 ring-indigo-200">
                                            @else
                                                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                                    {{ mb_substr($otherUser->name ?? '?', 0, 1) }}
                                                </span>
                                            @endif
                                            {{ $otherUser->name ?? 'สมาชิก' }}
                                        </span>
                                        @if($otherUser && $otherUser->exchange_area)
                                            <span class="text-slate-300">|</span>
                                            <span class="text-slate-600 font-medium">📍 {{ $otherUser->exchange_area }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Side by Side Books --}}
                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-11 items-center gap-4">
                                        
                                        {{-- My Book --}}
                                        <div class="md:col-span-5 bg-indigo-50/60 border border-indigo-200 rounded-2xl p-4 sm:p-5 flex gap-4 items-center">
                                            <div class="w-20 sm:w-24 aspect-[3/4] bg-white rounded-xl overflow-hidden shrink-0 shadow-xs border border-indigo-100">
                                                @if($myBook->image)
                                                    <img src="{{ asset('storage/' . $myBook->image) }}" alt="{{ $myBook->title }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-3xl bg-indigo-100/50 text-indigo-400">📖</div>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-indigo-700 text-white text-[11px] font-extrabold uppercase tracking-wider mb-1.5">
                                                    📚 หนังสือของคุณ
                                                </span>
                                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-2 leading-snug" title="{{ $myBook->title }}">
                                                    {{ $myBook->title }}
                                                </h4>
                                                <p class="text-xs text-slate-600 truncate mt-1">
                                                    ผู้แต่ง: <span class="font-semibold text-slate-800">{{ $myBook->author ?? 'ไม่ระบุ' }}</span>
                                                </p>
                                                @if($myBook->condition)
                                                    <span class="inline-block text-[11px] text-indigo-800 bg-white px-2.5 py-0.5 rounded-md border border-indigo-200 mt-2 font-bold">
                                                        สภาพ: {{ $myBook->condition }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Middle Transition --}}
                                        <div class="md:col-span-1 flex flex-col items-center justify-center my-[-4px] md:my-0">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-indigo-600 to-teal-600 text-white flex items-center justify-center text-xl shadow-lg shadow-indigo-500/25 animate-bounce">
                                                🔄
                                            </div>
                                        </div>

                                        {{-- Other Book --}}
                                        <div class="md:col-span-5 bg-amber-50/60 border border-amber-200 rounded-2xl p-4 sm:p-5 flex gap-4 items-center">
                                            <div class="w-20 sm:w-24 aspect-[3/4] bg-white rounded-xl overflow-hidden shrink-0 shadow-xs border border-amber-100">
                                                @if($otherBook->image)
                                                    <img src="{{ asset('storage/' . $otherBook->image) }}" alt="{{ $otherBook->title }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-3xl bg-amber-100/50 text-amber-400">📖</div>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-600 text-white text-[11px] font-extrabold uppercase tracking-wider mb-1.5">
                                                    ✨ หนังสือที่คุณจะได้รับ
                                                </span>
                                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-2 leading-snug" title="{{ $otherBook->title }}">
                                                    {{ $otherBook->title }}
                                                </h4>
                                                <p class="text-xs text-slate-600 truncate mt-1">
                                                    ผู้แต่ง: <span class="font-semibold text-slate-800">{{ $otherBook->author ?? 'ไม่ระบุ' }}</span>
                                                </p>
                                                @if($otherBook->condition)
                                                    <span class="inline-block text-[11px] text-amber-900 bg-white px-2.5 py-0.5 rounded-md border border-amber-200 mt-2 font-bold">
                                                        สภาพ: {{ $otherBook->condition }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Reason Note --}}
                                    <div class="mt-4 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm text-slate-700 flex items-center gap-2.5">
                                        <span class="text-lg shrink-0">💡</span>
                                        <span>
                                            คุณตามหา <strong>"{{ $myWanted->title ?? $otherBook->title }}"</strong> และอีกฝ่ายกำลังตามหา <strong>"{{ $otherWanted->title ?? $myBook->title }}"</strong>
                                        </span>
                                    </div>

                                    {{-- Action Row with Request Status Detection --}}
                                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div>
                                            @if($existingReq)
                                                @if($existingReq->status === 'pending')
                                                    @if($existingReq->requester_id === Auth::id())
                                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                            <span>⏳</span> <span>ส่งคำขอแล้ว (รออีกฝ่ายตอบรับ)</span>
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-indigo-100 text-indigo-900 border border-indigo-300">
                                                            <span>📬</span> <span>อีกฝ่ายส่งคำขอแลกเปลี่ยนมาถึงคุณแล้ว!</span>
                                                        </span>
                                                    @endif
                                                @elseif($existingReq->status === 'accepted')
                                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                        <span>🎉</span> <span>ยอมรับการแลกเปลี่ยนเรียบร้อยแล้ว</span>
                                                    </span>
                                                @elseif($existingReq->status === 'rejected')
                                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-red-100 text-red-900 border border-red-300">
                                                        <span>❌</span> <span>คำขอก่อนหน้าถูกปฏิเสธ (สามารถส่งคำขอใหม่ได้)</span>
                                                    </span>
                                                @endif
                                            @else
                                                <div class="text-xs sm:text-sm text-slate-500 font-medium">
                                                    กดส่งคำขอเพื่อเริ่มการแลกเปลี่ยน อีกฝ่ายจะได้รับการแจ้งเตือนทันที
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            @if($existingReq && $existingReq->status === 'pending')
                                                @if($existingReq->requester_id === Auth::id())
                                                    <a href="{{ route('exchange-requests.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs sm:text-sm font-bold border border-slate-300 transition-all">
                                                        <span>📋</span> <span>ดูในหน้ารายการคำขอ</span>
                                                    </a>
                                                @else
                                                    <form method="POST" action="{{ route('exchange-requests.accept', $existingReq->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-extrabold shadow-md hover:shadow-lg transition-all">
                                                            <span>✅</span> <span>ตอบรับคำขอแลกเปลี่ยน</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @elseif($existingReq && $existingReq->status === 'accepted')
                                                <a href="{{ route('chats.start', $existingReq->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs sm:text-sm font-extrabold transition-all">
                                                    <span>💬</span> <span>เปิดห้องแชตคู่แลกเปลี่ยน</span>
                                                </a>
                                            @else
                                                <form method="POST" action="{{ route('exchange-requests.store') }}" class="w-full sm:w-auto">
                                                    @csrf
                                                    <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                                                    <input type="hidden" name="offered_book_id" value="{{ $myBook->id }}">
                                                    <input type="hidden" name="requested_book_id" value="{{ $otherBook->id }}">

                                                    <button 
                                                        type="submit"
                                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 hover:from-indigo-700 hover:to-purple-800 active:scale-95 text-white font-extrabold text-xs sm:text-sm shadow-md hover:shadow-lg shadow-indigo-500/25 transition-all">
                                                        <span>🔄</span>
                                                        <span>ส่งคำขอแลกเปลี่ยนทันที</span>
                                                        <span>→</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-3xl border-2 border-slate-200 p-12 text-center text-slate-600 shadow-xs">
                        <div class="text-6xl mb-4">🎯</div>
                        <h3 class="text-xl font-extrabold text-slate-800">ยังไม่พบคู่แลกเปลี่ยนแบบ 2 ทางในขณะนี้</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
                            คุณสามารถดูแท็บ <strong>"จับคู่ลูกโซ่ 3 ทาง"</strong> หรือ <strong>"แนะนำความต้องการ 1 ทาง"</strong> เพื่อค้นหาโอกาสแลกเปลี่ยนเพิ่มเติมได้ครับ
                        </p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('books.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold shadow-sm transition-all">
                                <span>📚</span> <span>เพิ่มหนังสือที่คุณมี</span>
                            </a>
                            <a href="{{ route('wanted-books.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-bold shadow-sm transition-all">
                                <span>🔎</span> <span>เพิ่มหนังสือที่คุณตามหา</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ==========================================
                TAB 2: 3-WAY CIRCULAR TRADE RINGS
            =========================================== --}}
            <div x-show="activeTab === 'three_way'" x-transition class="space-y-6">
                @if (count($threeWayMatches) > 0)
                    <div class="space-y-8">
                        @foreach ($threeWayMatches as $idx => $match)
                            @php
                                $myBook = $match['my_book'];
                                $myReceiveBook = $match['my_receive_book'];
                                $p1User = $match['partner1_user'];
                                $p1GiveBook = $match['partner1_give_book'];
                                $p2User = $match['partner2_user'];
                            @endphp

                            <div class="bg-white rounded-3xl border-2 border-purple-300 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                                
                                {{-- Circular Header (High Contrast) --}}
                                @php
                                    $existingRing = $match['existing_trade_ring'] ?? null;
                                    $userStatus = $match['user_status'] ?? null;
                                    $acceptedCount = $match['accepted_count'] ?? 0;
                                    $isRingAccepted = ($existingRing && $existingRing->status === 'accepted');
                                    $isRingPending = ($existingRing && $existingRing->status === 'pending');
                                    $isRingRejected = ($existingRing && $existingRing->status === 'rejected');
                                    
                                    $p1Status = $existingRing ? $existingRing->getUserStatus($p1User->id) : 'pending';
                                    $p2Status = $existingRing ? $existingRing->getUserStatus($p2User->id) : 'pending';
                                @endphp

                                <div class="px-6 py-4 bg-gradient-to-r {{ $isRingAccepted ? 'from-emerald-700 via-teal-700 to-emerald-800' : 'from-purple-700 via-indigo-700 to-purple-800' }} text-white flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-white/20 text-white border border-white/30 backdrop-blur-md">
                                            <span>🌟</span>
                                            <span>วงจรแลกเปลี่ยน 3 ฝ่าย (3-Way Trade Ring #{{ $idx + 1 }})</span>
                                        </span>
                                        @if($isRingAccepted)
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-emerald-400 text-emerald-950 shadow-sm">
                                                🎉 ยืนยันครบ 3/3 ฝ่าย (สำเร็จ)
                                            </span>
                                        @elseif($isRingPending)
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-amber-400 text-amber-950 shadow-sm">
                                                ⏳ ยืนยันแล้ว {{ $acceptedCount }}/3 ท่าน
                                            </span>
                                        @elseif($isRingRejected)
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-red-400 text-red-950 shadow-sm">
                                                ❌ ข้อเสนอถูกยกเลิก
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-purple-200 text-purple-950 shadow-sm">
                                                ✨ พร้อมเริ่มต้นส่งคำขอ
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs sm:text-sm text-purple-100 font-bold flex items-center gap-1.5">
                                        <span>✨ แลกเปลี่ยนครบวงจร ทุกคนได้รับเล่มที่ต้องการ</span>
                                    </div>
                                </div>

                                {{-- Circular Flow Diagram --}}
                                <div class="p-6 sm:p-8 space-y-6">
                                    
                                    {{-- Visual Step Timeline --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 relative">
                                        
                                        {{-- Step 1: คุณมอบให้ Partner 1 --}}
                                        <div class="bg-indigo-50/70 border-2 border-indigo-200 rounded-2xl p-4 sm:p-5 flex flex-col justify-between relative shadow-xs">
                                            <div class="flex items-center justify-between gap-2 mb-3">
                                                <span class="px-2.5 py-1 rounded-full bg-indigo-600 text-white text-[11px] font-extrabold">
                                                    ขั้นตอนที่ 1: คุณส่งมอบ
                                                </span>
                                                <span class="text-xs font-black text-indigo-900 flex items-center gap-1">
                                                    คุณ ➔ {{ $p1User->name }}
                                                </span>
                                            </div>

                                            <div class="flex gap-3 items-center">
                                                <div class="w-16 aspect-[3/4] bg-white rounded-xl overflow-hidden shrink-0 shadow-xs border border-indigo-100">
                                                    @if($myBook->image)
                                                        <img src="{{ asset('storage/' . $myBook->image) }}" alt="{{ $myBook->title }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-xl bg-indigo-100 text-indigo-400">📖</div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 line-clamp-2 leading-snug">{{ $myBook->title }}</h5>
                                                    <p class="text-xs text-indigo-700 font-bold truncate mt-1">📚 หนังสือของคุณ</p>
                                                </div>
                                            </div>

                                            <div class="mt-3 pt-2.5 border-t border-indigo-200 text-xs text-indigo-900 flex items-center justify-between font-bold">
                                                <span>ผู้รับ: <strong class="text-slate-900">{{ $p1User->name }}</strong></span>
                                                <span>🎁 ส่งมอบ</span>
                                            </div>
                                        </div>

                                        {{-- Step 2: Partner 1 ส่งต่อ Partner 2 --}}
                                        <div class="bg-purple-50/70 border-2 border-purple-200 rounded-2xl p-4 sm:p-5 flex flex-col justify-between relative shadow-xs">
                                            <div class="flex items-center justify-between gap-2 mb-3">
                                                <span class="px-2.5 py-1 rounded-full bg-purple-600 text-white text-[11px] font-extrabold">
                                                    ขั้นตอนที่ 2: สมาชิกส่งต่อ
                                                </span>
                                                <span class="text-xs font-black text-purple-900 flex items-center gap-1">
                                                    {{ $p1User->name }} ➔ {{ $p2User->name }}
                                                </span>
                                            </div>

                                            <div class="flex gap-3 items-center">
                                                <div class="w-16 aspect-[3/4] bg-white rounded-xl overflow-hidden shrink-0 shadow-xs border border-purple-100">
                                                    @if($p1GiveBook->image)
                                                        <img src="{{ asset('storage/' . $p1GiveBook->image) }}" alt="{{ $p1GiveBook->title }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-xl bg-purple-100 text-purple-400">📖</div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 line-clamp-2 leading-snug">{{ $p1GiveBook->title }}</h5>
                                                    <p class="text-xs text-purple-700 font-bold truncate mt-1">ของ {{ $p1User->name }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-3 pt-2.5 border-t border-purple-200 text-xs text-purple-900 flex items-center justify-between font-bold">
                                                <span>ผู้รับ: <strong class="text-slate-900">{{ $p2User->name }}</strong></span>
                                                <span>🔄 ส่งต่อ</span>
                                            </div>
                                        </div>

                                        {{-- Step 3: Partner 2 ส่งให้ คุณ --}}
                                        <div class="bg-amber-50/70 border-2 border-amber-200 rounded-2xl p-4 sm:p-5 flex flex-col justify-between relative shadow-xs">
                                            <div class="flex items-center justify-between gap-2 mb-3">
                                                <span class="px-2.5 py-1 rounded-full bg-amber-600 text-white text-[11px] font-extrabold">
                                                    ขั้นตอนที่ 3: คุณได้รับ
                                                </span>
                                                <span class="text-xs font-black text-amber-900 flex items-center gap-1">
                                                    {{ $p2User->name }} ➔ คุณ
                                                </span>
                                            </div>

                                            <div class="flex gap-3 items-center">
                                                <div class="w-16 aspect-[3/4] bg-white rounded-xl overflow-hidden shrink-0 shadow-xs border border-amber-100">
                                                    @if($myReceiveBook->image)
                                                        <img src="{{ asset('storage/' . $myReceiveBook->image) }}" alt="{{ $myReceiveBook->title }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-xl bg-amber-100 text-amber-400">📖</div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 line-clamp-2 leading-snug">{{ $myReceiveBook->title }}</h5>
                                                    <p class="text-xs text-amber-700 font-extrabold truncate mt-1">✨ คุณได้รับเล่มนี้!</p>
                                                </div>
                                            </div>

                                            <div class="mt-3 pt-2.5 border-t border-amber-200 text-xs text-amber-900 flex items-center justify-between font-bold">
                                                <span>จาก: <strong class="text-slate-900">{{ $p2User->name }}</strong></span>
                                                <span>🎉 รับหนังสือ</span>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- ==========================================
                                        CONFIRMATION FLOW & PROGRESS SECTION
                                    =========================================== --}}
                                    <div class="p-5 sm:p-6 bg-slate-900 text-white rounded-2xl shadow-sm space-y-4">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                                            <div>
                                                <h4 class="font-extrabold text-sm sm:text-base flex items-center gap-2 text-white">
                                                    <span>📋</span>
                                                    <span>สถานะการยืนยันข้อเสนอแลกเปลี่ยน 3 ฝ่าย</span>
                                                </h4>
                                                <p class="text-xs text-slate-300 mt-1">
                                                    การแลกเปลี่ยนจะสำเร็จและเปิดเผยข้อมูลติดต่อเมื่อสมาชิกทั้ง 3 ท่านกดยืนยันยอมรับครบทุกคน
                                                </p>
                                            </div>

                                            {{-- Member Verification Badges --}}
                                            <div class="flex flex-wrap items-center gap-2">
                                                <div class="px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-1.5 {{ $userStatus === 'accepted' ? 'bg-emerald-950/80 border-emerald-500 text-emerald-300' : 'bg-slate-800 border-slate-700 text-slate-300' }}">
                                                    <span>{{ $userStatus === 'accepted' ? '✅' : '⏳' }}</span>
                                                    <span>คุณ: {{ $userStatus === 'accepted' ? 'ยืนยันแล้ว' : 'รอยืนยัน' }}</span>
                                                </div>
                                                <div class="px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-1.5 {{ ($existingRing && $p1Status === 'accepted') ? 'bg-emerald-950/80 border-emerald-500 text-emerald-300' : 'bg-slate-800 border-slate-700 text-slate-300' }}">
                                                    <span>{{ ($existingRing && $p1Status === 'accepted') ? '✅' : '⏳' }}</span>
                                                    <span>{{ $p1User->name }}: {{ ($existingRing && $p1Status === 'accepted') ? 'ยืนยันแล้ว' : 'รอยืนยัน' }}</span>
                                                </div>
                                                <div class="px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-1.5 {{ ($existingRing && $p2Status === 'accepted') ? 'bg-emerald-950/80 border-emerald-500 text-emerald-300' : 'bg-slate-800 border-slate-700 text-slate-300' }}">
                                                    <span>{{ ($existingRing && $p2Status === 'accepted') ? '✅' : '⏳' }}</span>
                                                    <span>{{ $p2User->name }}: {{ ($existingRing && $p2Status === 'accepted') ? 'ยืนยันแล้ว' : 'รอยืนยัน' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Confirmation Action Buttons --}}
                                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-1">
                                            @if(!$existingRing)
                                                <div class="text-xs text-slate-300 flex items-center gap-2">
                                                    <span class="text-lg">💡</span>
                                                    <span>กดส่งคำขอเพื่อเริ่มต้นวงจรแลกเปลี่ยน 3 ฝ่าย (ระบบจะยืนยันในส่วนของคุณให้อัตโนมัติ)</span>
                                                </div>

                                                <form method="POST" action="{{ route('trade-rings.store') }}" class="w-full sm:w-auto">
                                                    @csrf
                                                    <input type="hidden" name="user1_id" value="{{ auth()->id() }}">
                                                    <input type="hidden" name="book1_id" value="{{ $myBook->id }}">
                                                    <input type="hidden" name="user2_id" value="{{ $p1User->id }}">
                                                    <input type="hidden" name="book2_id" value="{{ $p1GiveBook->id }}">
                                                    <input type="hidden" name="user3_id" value="{{ $p2User->id }}">
                                                    <input type="hidden" name="book3_id" value="{{ $myReceiveBook->id }}">

                                                    <button 
                                                        type="submit"
                                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 hover:from-purple-500 hover:to-indigo-600 active:scale-95 text-white font-black text-xs sm:text-sm shadow-lg shadow-purple-600/30 transition-all">
                                                        <span>🔄</span>
                                                        <span>เริ่มต้นส่งคำขอยืนยันแลกเปลี่ยน 3 ฝ่าย</span>
                                                        <span>→</span>
                                                    </button>
                                                </form>
                                            @elseif($isRingPending)
                                                @if($userStatus === 'pending')
                                                    <div class="text-xs text-amber-300 flex items-center gap-2">
                                                        <span class="text-lg animate-bounce">🔔</span>
                                                        <span>มีสมาชิกส่งข้อเสนอวงจร 3 ฝ่ายนี้แล้ว กรุณากดยืนยันการแลกเปลี่ยนเพื่อดำเนินการต่อ</span>
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                                                        <form method="POST" action="{{ route('trade-rings.accept', $existingRing->id) }}" class="flex-1 sm:flex-none">
                                                            @csrf
                                                            <button 
                                                                type="submit"
                                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-black text-xs sm:text-sm shadow-lg shadow-emerald-600/30 transition-all">
                                                                <span>✅</span>
                                                                <span>กดยืนยันยอมรับการแลกเปลี่ยน 3 ฝ่าย</span>
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="{{ route('trade-rings.reject', $existingRing->id) }}" class="flex-1 sm:flex-none">
                                                            @csrf
                                                            <button 
                                                                type="submit"
                                                                onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการปฏิเสธข้อเสนอนี้?')"
                                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-800 hover:bg-red-900/60 text-slate-300 hover:text-red-200 border border-slate-700 font-bold text-xs sm:text-sm transition-all">
                                                                <span>❌</span>
                                                                <span>ปฏิเสธ</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="text-xs text-emerald-300 flex items-center gap-2">
                                                        <span class="text-lg">⏳</span>
                                                        <span><strong>คุณยืนยันแล้ว:</strong> ระบบกำลังรอสมาชิกอีก <strong>{{ 3 - $acceptedCount }}</strong> ท่านกดยืนยันยอมรับข้อเสนอ</span>
                                                    </div>

                                                    <form method="POST" action="{{ route('trade-rings.reject', $existingRing->id) }}" class="w-full sm:w-auto">
                                                        @csrf
                                                        <button 
                                                            type="submit"
                                                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกคำขอนี้?')"
                                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-slate-800 hover:bg-red-900/60 text-slate-400 hover:text-red-300 border border-slate-700 font-bold text-xs transition-all">
                                                            <span>❌</span>
                                                            <span>ยกเลิกคำขอ</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @elseif($isRingAccepted)
                                                <div class="text-xs text-emerald-300 flex items-center gap-2">
                                                    <span class="text-lg">🎉</span>
                                                    <span><strong>แลกเปลี่ยนสำเร็จ:</strong> สมาชิกทุกคนยืนยันเรียบร้อยแล้ว หนังสือทั้ง 3 เล่มเปลี่ยนสถานะเป็นแลกเปลี่ยนแล้ว</span>
                                                </div>

                                                <a href="{{ route('exchange-requests.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs sm:text-sm shadow-md transition-all">
                                                    <span>📦</span>
                                                    <span>ดูประวัติการแลกเปลี่ยน</span>
                                                </a>
                                            @else
                                                <div class="text-xs text-red-300 flex items-center gap-2">
                                                    <span>❌</span>
                                                    <span>ข้อเสนอนี้ถูกยกเลิกไปแล้ว</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ==========================================
                                        CONTACT INFORMATION SECTION (LOCKED vs UNLOCKED)
                                    =========================================== --}}
                                    @if($isRingAccepted)
                                        {{-- UNLOCKED: Show Contact Info --}}
                                        <div class="p-5 sm:p-6 bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 rounded-2xl border-2 border-emerald-300 space-y-4">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                <div class="flex items-center gap-2 text-sm font-extrabold text-emerald-950">
                                                    <span class="text-xl">🔓</span>
                                                    <span>ข้อมูลสมาชิกในวงจรแลกเปลี่ยน 3 ฝ่าย (เปิดเผยแล้วเพื่อประสานงานส่งมอบ):</span>
                                                </div>
                                                <span class="text-xs text-emerald-900 bg-emerald-100 border border-emerald-300 px-3 py-1 rounded-full font-black">
                                                    ✅ ยืนยันครบทั้ง 3 ฝ่ายแล้ว
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                                {{-- Partner 1 Contact --}}
                                                <div class="p-4 bg-white rounded-xl border border-emerald-200 shadow-2xs space-y-2">
                                                    <div class="flex items-center gap-3">
                                                        @if($p1User->avatar)
                                                            <img src="{{ asset('storage/' . $p1User->avatar) }}" alt="{{ $p1User->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-emerald-200">
                                                        @else
                                                            <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-extrabold text-sm flex items-center justify-center">
                                                                {{ mb_substr($p1User->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="text-sm font-extrabold text-slate-900">{{ $p1User->name }} (คนที่ 1)</p>
                                                            <p class="text-xs text-indigo-600 font-bold">คุณส่งหนังสือให้ท่านนี้ ➔</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-slate-700 space-y-1.5 pt-2 border-t border-slate-100">
                                                        <p>📞 โทร: <a href="tel:{{ $p1User->phone }}" class="font-bold text-indigo-600 hover:underline">{{ $p1User->phone ?? 'ไม่ระบุ' }}</a></p>
                                                        <p>💬 Line: <span class="font-bold text-slate-900">{{ $p1User->line_id ?? 'ไม่ระบุ' }}</span></p>
                                                        <p>📍 จุดสะดวก: <span class="font-bold text-slate-900">{{ $p1User->exchange_area ?? 'ไม่ระบุ' }}</span></p>
                                                    </div>
                                                </div>

                                                {{-- Partner 2 Contact --}}
                                                <div class="p-4 bg-white rounded-xl border border-emerald-200 shadow-2xs space-y-2">
                                                    <div class="flex items-center gap-3">
                                                        @if($p2User->avatar)
                                                            <img src="{{ asset('storage/' . $p2User->avatar) }}" alt="{{ $p2User->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-amber-200">
                                                        @else
                                                            <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 font-extrabold text-sm flex items-center justify-center">
                                                                {{ mb_substr($p2User->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="text-sm font-extrabold text-slate-900">{{ $p2User->name }} (คนที่ 2)</p>
                                                            <p class="text-xs text-amber-600 font-bold">ท่านนี้ส่งหนังสือให้คุณ ➔</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-slate-700 space-y-1.5 pt-2 border-t border-slate-100">
                                                        <p>📞 โทร: <a href="tel:{{ $p2User->phone }}" class="font-bold text-amber-600 hover:underline">{{ $p2User->phone ?? 'ไม่ระบุ' }}</a></p>
                                                        <p>💬 Line: <span class="font-bold text-slate-900">{{ $p2User->line_id ?? 'ไม่ระบุ' }}</span></p>
                                                        <p>📍 จุดสะดวก: <span class="font-bold text-slate-900">{{ $p2User->exchange_area ?? 'ไม่ระบุ' }}</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-xs sm:text-sm text-emerald-900 bg-white/90 p-3.5 rounded-xl border border-emerald-200 flex items-start gap-2.5 font-medium">
                                                <span class="text-xl leading-none">💡</span>
                                                <span>
                                                    <strong>คำแนะนำการส่งมอบ 3 ทาง:</strong> คุณสามารถโทรศัพท์หรือแอด Line เพื่อประสานงานจัดส่งหนังสือให้ <strong>{{ $p1User->name }}</strong> และรับหนังสือจาก <strong>{{ $p2User->name }}</strong> ตามที่อยู่ที่ตกลงกันได้ทันที
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        {{-- LOCKED: Hide Contact Info Until Accepted --}}
                                        <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-50 via-slate-100 to-slate-50 rounded-2xl border-2 border-dashed border-slate-300 space-y-4">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-800">
                                                    <span class="text-xl">🔒</span>
                                                    <span>ข้อมูลติดต่อถูกล็อคไว้เพื่อความเป็นส่วนตัวและความปลอดภัย</span>
                                                </div>
                                                <span class="text-xs text-slate-700 bg-white border border-slate-300 px-3 py-1 rounded-full font-bold">
                                                    🔒 จะเปิดเผยเมื่อยืนยันครบ 3/3 คน
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                                {{-- Partner 1 Locked Contact --}}
                                                <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2 opacity-90">
                                                    <div class="flex items-center gap-3">
                                                        @if($p1User->avatar)
                                                            <img src="{{ asset('storage/' . $p1User->avatar) }}" alt="{{ $p1User->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-200">
                                                        @else
                                                            <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-extrabold text-sm flex items-center justify-center">
                                                                {{ mb_substr($p1User->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="text-sm font-extrabold text-slate-900">{{ $p1User->name }} (คนที่ 1)</p>
                                                            <p class="text-xs text-slate-500 font-medium">คุณส่งหนังสือให้ท่านนี้</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-slate-500 space-y-1.5 pt-2 border-t border-slate-100">
                                                        <div class="flex items-center justify-between py-1 bg-slate-50 px-2 rounded-md">
                                                            <span>📞 โทร:</span>
                                                            <span class="font-bold text-slate-400">🔒 ซ่อนไว้จนกว่าจะยืนยันครบ</span>
                                                        </div>
                                                        <div class="flex items-center justify-between py-1 bg-slate-50 px-2 rounded-md">
                                                            <span>💬 Line ID:</span>
                                                            <span class="font-bold text-slate-400">🔒 ซ่อนไว้จนกว่าจะยืนยันครบ</span>
                                                        </div>
                                                        <div class="flex items-center justify-between py-1 bg-slate-50 px-2 rounded-md">
                                                            <span>📍 พื้นที่สะดวก:</span>
                                                            <span class="font-bold text-slate-700">{{ $p1User->exchange_area ?? 'ไม่ระบุ' }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Partner 2 Locked Contact --}}
                                                <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2 opacity-90">
                                                    <div class="flex items-center gap-3">
                                                        @if($p2User->avatar)
                                                            <img src="{{ asset('storage/' . $p2User->avatar) }}" alt="{{ $p2User->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-200">
                                                        @else
                                                            <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-extrabold text-sm flex items-center justify-center">
                                                                {{ mb_substr($p2User->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="text-sm font-extrabold text-slate-900">{{ $p2User->name }} (คนที่ 2)</p>
                                                            <p class="text-xs text-slate-500 font-medium">ท่านนี้ส่งหนังสือให้คุณ</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-slate-500 space-y-1.5 pt-2 border-t border-slate-100">
                                                        <div class="flex items-center justify-between py-1 bg-slate-50 px-2 rounded-md">
                                                            <span>📞 โทร:</span>
                                                            <span class="font-bold text-slate-400">🔒 ซ่อนไว้จนกว่าจะยืนยันครบ</span>
                                                        </div>
                                                        <div class="flex items-center justify-between py-1 bg-slate-50 px-2 rounded-md">
                                                            <span>💬 Line ID:</span>
                                                            <span class="font-bold text-slate-400">🔒 ซ่อนไว้จนกว่าจะยืนยันครบ</span>
                                                        </div>
                                                        <div class="flex items-center justify-between py-1 bg-slate-50 px-2 rounded-md">
                                                            <span>📍 พื้นที่สะดวก:</span>
                                                            <span class="font-bold text-slate-700">{{ $p2User->exchange_area ?? 'ไม่ระบุ' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-xs text-slate-600 bg-white p-3.5 rounded-xl border border-slate-200 flex items-start gap-2.5">
                                                <span class="text-lg leading-none">🛡️</span>
                                                <span>
                                                    <strong>ระบบปกป้องข้อมูลส่วนตัว:</strong> เพื่อความปลอดภัย เบอร์โทรศัพท์และ LINE ID ของสมาชิกจะแสดงให้เห็นเฉพาะเมื่อทุกฝ่ายกดยืนยันยอมรับการแลกเปลี่ยนครบทั้ง 3 ท่านเท่านั้น
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-3xl border-2 border-slate-200 p-12 text-center text-slate-600 shadow-xs">
                        <div class="text-6xl mb-4">🔄</div>
                        <h3 class="text-xl font-extrabold text-slate-800">ยังไม่พบวงจรแลกเปลี่ยน 3 ทางในขณะนี้</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
                            วงจร 3 ทางจะเกิดขึ้นอัตโนมัติเมื่อสมาชิก 3 คนมีความต้องการเชื่อมต่อกันเป็นวงกลม (A ➔ B ➔ C ➔ A) ยิ่งคุณลงหนังสือและประกาศหามาก โอกาสเกิดวงจรยิ่งสูงขึ้น
                        </p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('books.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold shadow-sm transition-all">
                                <span>📚</span> <span>เพิ่มหนังสือที่คุณมี</span>
                            </a>
                            <a href="{{ route('wanted-books.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-bold shadow-sm transition-all">
                                <span>🔎</span> <span>เพิ่มหนังสือที่คุณตามหา</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ==========================================
                TAB 3: 1-WAY WISHLIST & DEMAND MATCHES
            =========================================== --}}
            <div x-show="activeTab === 'one_way'" x-transition class="space-y-8">
                
                {{-- Section 1: Books you want that are available in platform --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-extrabold text-base sm:text-lg text-slate-900 flex items-center gap-2">
                            <span>📖</span>
                            <span>หนังสือที่คุณตามหา (มีสมาชิกพร้อมแลกในระบบ)</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300">
                                {{ count($oneWayMatches['wishlist_matches']) }} เล่ม
                            </span>
                        </h4>
                    </div>

                    @if(count($oneWayMatches['wishlist_matches']) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($oneWayMatches['wishlist_matches'] as $item)
                                @php
                                    $wanted = $item['wanted'];
                                    $availBook = $item['available_book'];
                                    $owner = $item['owner'];
                                @endphp
                                <div class="bg-white rounded-2xl border-2 border-slate-200 p-4 shadow-xs hover:shadow-md transition-all flex gap-4 items-center">
                                    <div class="w-18 aspect-[3/4] bg-slate-100 rounded-xl overflow-hidden shrink-0 shadow-2xs border border-slate-200">
                                        @if($availBook->image)
                                            <img src="{{ asset('storage/' . $availBook->image) }}" alt="{{ $availBook->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-2xl bg-amber-50 text-amber-400">📖</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                พร้อมแลก
                                            </span>
                                            @if($availBook->condition)
                                                <span class="text-xs text-slate-600 font-medium">สภาพ: {{ $availBook->condition }}</span>
                                            @endif
                                        </div>
                                        <h5 class="font-bold text-sm text-slate-900 line-clamp-1" title="{{ $availBook->title }}">
                                            {{ $availBook->title }}
                                        </h5>
                                        <p class="text-xs text-slate-600 truncate">เจ้าของ: <strong class="text-slate-900">{{ $owner->name }}</strong></p>
                                        @if($owner->exchange_area)
                                            <p class="text-xs text-slate-500 font-medium truncate">📍 {{ $owner->exchange_area }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 bg-slate-50 border border-slate-200 rounded-2xl text-center text-slate-500 text-xs sm:text-sm font-medium">
                            ยังไม่มีหนังสือใน Wishlist ของคุณที่สมาชิกคนอื่นลงพร้อมแลก
                        </div>
                    @endif
                </div>

                {{-- Section 2: Members looking for books you have --}}
                <div class="space-y-4 pt-6 border-t-2 border-slate-200">
                    <div class="flex items-center justify-between">
                        <h4 class="font-extrabold text-base sm:text-lg text-slate-900 flex items-center gap-2">
                            <span>📢</span>
                            <span>มีสมาชิกกำลังตามหาหนังสือที่คุณมี</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-100 text-indigo-900 border border-indigo-300">
                                {{ count($oneWayMatches['demand_matches']) }} รายการ
                            </span>
                        </h4>
                    </div>

                    @if(count($oneWayMatches['demand_matches']) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($oneWayMatches['demand_matches'] as $item)
                                @php
                                    $myBook = $item['my_book'];
                                    $wanted = $item['wanted'];
                                    $seeker = $item['seeker'];
                                @endphp
                                <div class="bg-white rounded-2xl border-2 border-slate-200 p-4 shadow-xs hover:shadow-md transition-all flex gap-4 items-center">
                                    <div class="w-18 aspect-[3/4] bg-slate-100 rounded-xl overflow-hidden shrink-0 shadow-2xs border border-slate-200">
                                        @if($myBook->image)
                                            <img src="{{ asset('storage/' . $myBook->image) }}" alt="{{ $myBook->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-2xl bg-indigo-50 text-indigo-400">📖</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-900 border border-indigo-300">
                                                หนังสือของคุณ
                                            </span>
                                        </div>
                                        <h5 class="font-bold text-sm text-slate-900 line-clamp-1" title="{{ $myBook->title }}">
                                            {{ $myBook->title }}
                                        </h5>
                                        <p class="text-xs text-slate-600 truncate">
                                            ผู้ตามหา: <strong class="text-slate-900">{{ $seeker->name }}</strong>
                                        </p>
                                        @if($seeker->exchange_area)
                                            <p class="text-xs text-slate-500 font-medium truncate">📍 สะดวก: {{ $seeker->exchange_area }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 bg-slate-50 border border-slate-200 rounded-2xl text-center text-slate-500 text-xs sm:text-sm font-medium">
                            ยังไม่มีผู้ใช้คนอื่นประกาศตามหาหนังสือของคุณในขณะนี้
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
