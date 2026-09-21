<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>🔄</span>
                    <span>จัดการคำขอแลกเปลี่ยนหนังสือ</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    ตรวจสอบและตอบรับคำขอแลกเปลี่ยนหนังสือระหว่างคุณกับสมาชิกคนอื่น
                </p>
            </div>

            <a href="{{ route('matching.index') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition-all self-start sm:self-auto">
                <span>🎯</span>
                <span>ค้นหาคู่แลกเปลี่ยนใหม่</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ activeTab: 'received' }">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🎉</span>
                        <div>
                            <p class="font-bold text-sm">สำเร็จ!</p>
                            <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-lg p-1">
                        ✕
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <p class="font-bold text-sm">ไม่สำเร็จ!</p>
                            <p class="text-xs text-red-700 mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700 text-lg p-1">
                        ✕
                    </button>
                </div>
            @endif

            {{-- Tab Switcher --}}
            <div class="bg-white p-1.5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-2 max-w-2xl mx-auto">
                <button 
                    type="button"
                    @click="activeTab = 'received'"
                    :class="activeTab === 'received' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 py-2.5 px-3 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all duration-150">
                    <span>📥 คำขอที่ได้รับ</span>
                    <span :class="activeTab === 'received' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'" class="px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $receivedRequests->count() }}
                    </span>
                </button>

                <button 
                    type="button"
                    @click="activeTab = 'sent'"
                    :class="activeTab === 'sent' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 py-2.5 px-3 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all duration-150">
                    <span>📤 คำขอที่ส่งไป</span>
                    <span :class="activeTab === 'sent' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'" class="px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $sentRequests->count() }}
                    </span>
                </button>

                <button 
                    type="button"
                    @click="activeTab = 'trade_rings'"
                    :class="activeTab === 'trade_rings' ? 'bg-purple-700 text-white shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 py-2.5 px-3 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all duration-150">
                    <span>🔄 วงจร 3 ฝ่าย</span>
                    <span :class="activeTab === 'trade_rings' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'" class="px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ $tradeRings->count() }}
                    </span>
                </button>
            </div>

            {{-- ========================================================= --}}
            {{-- 1. TAB: RECEIVED REQUESTS --}}
            {{-- ========================================================= --}}
            <div x-show="activeTab === 'received'" x-cloak class="space-y-5">

                @if($receivedRequests->count() > 0)

                    @foreach($receivedRequests as $request)

                        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-md transition-all overflow-hidden">
                            
                            {{-- Request Card Header --}}
                            <div class="px-5 py-3.5 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">

                                <div class="flex items-center gap-2.5">

                                    @if($request->requester && $request->requester->avatar)

                                        <img 
                                            src="{{ asset('storage/' . $request->requester->avatar) }}" 
                                            alt="{{ $request->requester->name }}" 
                                            class="w-8 h-8 rounded-full object-cover shadow-xs ring-1 ring-slate-200 shrink-0"
                                        >

                                    @else

                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-xs font-bold flex items-center justify-center shadow-xs shrink-0">
                                            {{ mb_substr($request->requester->name ?? '?', 0, 1) }}
                                        </div>

                                    @endif

                                    <div>
                                        <p class="text-xs sm:text-sm font-bold text-slate-800">
                                            {{ $request->requester->name ?? 'สมาชิก' }}
                                        </p>

                                        <p class="text-[11px] text-slate-400">
                                            ส่งเมื่อ {{ $request->created_at->locale('th')->diffForHumans() }}
                                        </p>
                                    </div>

                                </div>

                                {{-- Status Badge & Chat Button --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('chats.start', $request->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-50 hover:bg-indigo-100 active:scale-95 text-indigo-700 font-bold text-xs shadow-2xs transition-all">
                                        <span>💬</span>
                                        <span>แชตพูดคุย</span>
                                    </a>

                                    @if($request->status === 'pending')

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            ⏳ รอดำเนินการตอบรับ
                                        </span>

                                    @elseif($request->status === 'accepted')

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            ✅ คุณยอมรับคำขอนี้แล้ว
                                        </span>

                                    @elseif($request->status === 'completed')

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            <span>🎉</span>
                                            แลกเปลี่ยนสำเร็จ
                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                            ❌ คุณปฏิเสธคำขอนี้แล้ว
                                        </span>

                                    @endif

                                </div>

                            </div>

                            {{-- Center Book Comparison --}}
                            <div class="p-5 sm:p-6">

                                <div class="bg-gradient-to-r from-slate-50 via-indigo-50/30 to-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-100 flex flex-col md:flex-row items-center justify-center gap-4 sm:gap-8">

                                    {{-- Left: Offered Book --}}
                                    <div class="flex-1 w-full max-w-xs flex items-center gap-3 sm:gap-4 bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-2xs">

                                        <div class="w-18 sm:w-20 aspect-[3/4] bg-slate-100 rounded-lg overflow-hidden shrink-0 shadow-xs">

                                            @if($request->offeredBook && $request->offeredBook->image)

                                                <img 
                                                    src="{{ asset('storage/' . $request->offeredBook->image) }}" 
                                                    alt="{{ $request->offeredBook->title }}" 
                                                    class="w-full h-full object-cover"
                                                >

                                            @else

                                                <div class="w-full h-full flex items-center justify-center text-2xl bg-indigo-50 text-indigo-400">
                                                    📖
                                                </div>

                                            @endif

                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <span class="inline-block px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                                                เขาเสนอให้คุณ
                                            </span>

                                            <h4 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-2 leading-snug" title="{{ $request->offeredBook->title ?? '-' }}">
                                                {{ $request->offeredBook->title ?? 'หนังสือถูกลบ' }}
                                            </h4>

                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                {{ $request->offeredBook->author ?? 'ไม่ระบุผู้แต่ง' }}
                                            </p>

                                            @if($request->offeredBook && $request->offeredBook->condition)

                                                <span class="inline-block text-[10px] text-slate-400 mt-1">
                                                    สภาพ: {{ $request->offeredBook->condition }}
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                    {{-- Middle --}}
                                    <div class="flex flex-col items-center justify-center shrink-0 my-[-6px] md:my-0">

                                        <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center text-lg shadow-md shadow-indigo-500/30">
                                            🔄
                                        </div>

                                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-1">
                                            แลกเปลี่ยนกับ
                                        </span>

                                    </div>

                                    {{-- Right --}}
                                    <div class="flex-1 w-full max-w-xs flex items-center gap-3 sm:gap-4 bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-2xs">

                                        <div class="w-18 sm:w-20 aspect-[3/4] bg-slate-100 rounded-lg overflow-hidden shrink-0 shadow-xs">

                                            @if($request->requestedBook && $request->requestedBook->image)

                                                <img 
                                                    src="{{ asset('storage/' . $request->requestedBook->image) }}" 
                                                    alt="{{ $request->requestedBook->title }}" 
                                                    class="w-full h-full object-cover"
                                                >

                                            @else

                                                <div class="w-full h-full flex items-center justify-center text-2xl bg-purple-50 text-purple-400">
                                                    📖
                                                </div>

                                            @endif

                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <span class="inline-block px-2 py-0.5 rounded bg-purple-50 text-purple-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                                                หนังสือของคุณที่เขาอยากได้
                                            </span>

                                            <h4 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-2 leading-snug" title="{{ $request->requestedBook->title ?? '-' }}">
                                                {{ $request->requestedBook->title ?? 'หนังสือถูกลบ' }}
                                            </h4>

                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                {{ $request->requestedBook->author ?? 'ไม่ระบุผู้แต่ง' }}
                                            </p>

                                            @if($request->requestedBook && $request->requestedBook->condition)

                                                <span class="inline-block text-[10px] text-slate-400 mt-1">
                                                    สภาพ: {{ $request->requestedBook->condition }}
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                                {{-- ================================================= --}}
                                {{-- IN-APP CHAT BANNER : RECEIVED / ACCEPTED OR COMPLETED --}}
                                {{-- ================================================= --}}
                                @if($request->status === 'accepted' || $request->status === 'completed')

                                    <div class="mt-5 p-5 bg-gradient-to-r from-indigo-50/90 via-purple-50/60 to-indigo-50/90 border border-indigo-200/90 rounded-2xl shadow-xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div class="flex items-center gap-3.5">
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center text-2xl shadow-md shadow-indigo-500/20 shrink-0">
                                                    💬
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-sm sm:text-base text-slate-800">
                                                        ห้องแชตประสานงาน BookCycle
                                                    </h4>
                                                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                                        พูดคุย ส่งรูปภาพ หรือนัดหมายรับหนังสือได้ในระบบทันที ไม่จำเป็นต้องเปิดเผยข้อมูลส่วนตัว
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('chats.start', $request->id) }}" 
                                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-500/20 transition-all">
                                                    <span>💬</span>
                                                    <span>เปิดห้องแชตพูดคุยทันที</span>
                                                    <span>→</span>
                                                </a>

                                                @php
                                                    $iConfirmed = ($request->requester_id === Auth::id() && $request->requester_confirmed_at) || ($request->receiver_id === Auth::id() && $request->receiver_confirmed_at);
                                                @endphp

                                                @if($request->status === 'completed')
                                                    <span class="px-3.5 py-2 bg-purple-100 text-purple-800 rounded-xl text-xs font-bold flex items-center gap-1">
                                                        <span>🎉</span> <span>แลกเปลี่ยนสำเร็จแล้ว</span>
                                                    </span>
                                                @elseif($iConfirmed)
                                                    <span class="px-3.5 py-2 bg-emerald-100 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-1">
                                                        <span>✓</span> <span>คุณยืนยันรับหนังสือแล้ว</span>
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('exchange-requests.confirm-received', $request->id) }}" class="inline" onsubmit="return confirm('ยืนยันว่าคุณได้รับหนังสือเรียบร้อยแล้ว?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                                            <span>📦</span>
                                                            <span>ยืนยันได้รับหนังสือแล้ว</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>



                                @endif

                                {{-- Action Buttons --}}
                                @if($request->status === 'pending')

                                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-3">

                                        <form method="POST" action="{{ route('exchange-requests.accept', $request) }}" class="w-full sm:w-auto">

                                            @csrf
                                            @method('PATCH')

                                            <button 
                                                type="submit"
                                                onclick="return confirm('ยืนยันยอมรับการแลกเปลี่ยนหนังสือเล่มนี้?');"
                                                class="w-full sm:w-56 inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow transition-all"
                                            >
                                                <span>✅</span>
                                                <span>ยอมรับการแลกเปลี่ยน</span>
                                            </button>

                                        </form>

                                        <form method="POST" action="{{ route('exchange-requests.reject', $request) }}" class="w-full sm:w-auto">

                                            @csrf
                                            @method('PATCH')

                                            <button 
                                                type="submit"
                                                onclick="return confirm('ต้องการปฏิเสธคำขอนี้ใช่หรือไม่?');"
                                                class="w-full sm:w-48 inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-700 hover:border-red-200 border border-slate-200 font-bold text-sm transition-all"
                                            >
                                                <span>❌</span>
                                                <span>ปฏิเสธคำขอ</span>
                                            </button>

                                        </form>

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                @else

                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-500 shadow-xs">

                        <div class="text-5xl mb-3">
                            📭
                        </div>

                        <h3 class="font-bold text-base text-slate-700">
                            ยังไม่มีคำขอแลกเปลี่ยนเข้ามา
                        </h3>

                        <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-sm mx-auto">
                            เมื่อมีเพื่อนสมาชิกสนใจแลกเปลี่ยนหนังสือของคุณ คำขอจะแสดงขึ้นที่นี่
                        </p>

                    </div>

                @endif

            </div>


            {{-- ========================================================= --}}
            {{-- 2. TAB: SENT REQUESTS --}}
            {{-- ========================================================= --}}
            <div x-show="activeTab === 'sent'" x-cloak class="space-y-5">

                @if($sentRequests->count() > 0)

                    @foreach($sentRequests as $request)

                        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-md transition-all overflow-hidden">

                            {{-- Request Card Header --}}
                            <div class="px-5 py-3.5 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">

                                <div class="flex items-center gap-2.5">

                                    @if($request->receiver && $request->receiver->avatar)

                                        <img 
                                            src="{{ asset('storage/' . $request->receiver->avatar) }}" 
                                            alt="{{ $request->receiver->name }}" 
                                            class="w-8 h-8 rounded-full object-cover shadow-xs ring-1 ring-slate-200 shrink-0"
                                        >

                                    @else

                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-500 to-indigo-600 text-white text-xs font-bold flex items-center justify-center shadow-xs shrink-0">
                                            {{ mb_substr($request->receiver->name ?? '?', 0, 1) }}
                                        </div>

                                    @endif

                                    <div>

                                        <p class="text-xs sm:text-sm font-bold text-slate-800">
                                            ส่งถึง: {{ $request->receiver->name ?? 'สมาชิก' }}
                                        </p>

                                        <p class="text-[11px] text-slate-400">
                                            ส่งเมื่อ {{ $request->created_at->locale('th')->diffForHumans() }}
                                        </p>

                                    </div>

                                </div>

                                {{-- Status Badge & Chat Button --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('chats.start', $request->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-50 hover:bg-indigo-100 active:scale-95 text-indigo-700 font-bold text-xs shadow-2xs transition-all">
                                        <span>💬</span>
                                        <span>แชตพูดคุย</span>
                                    </a>

                                    @if($request->status === 'pending')

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            ⏳ กำลังรอการตอบรับจากอีกฝ่าย
                                        </span>

                                    @elseif($request->status === 'accepted')

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            ✅ คำขอได้รับการตอบรับแล้ว
                                        </span>

                                    @elseif($request->status === 'completed')

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            <span>🎉</span>
                                            แลกเปลี่ยนสำเร็จ
                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                            ❌ คำขอถูกปฏิเสธ
                                        </span>

                                    @endif

                                </div>

                            </div>

                            {{-- Center Book Comparison --}}
                            <div class="p-5 sm:p-6">

                                <div class="bg-gradient-to-r from-slate-50 via-amber-50/30 to-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-100 flex flex-col md:flex-row items-center justify-center gap-4 sm:gap-8">

                                    {{-- Left --}}
                                    <div class="flex-1 w-full max-w-xs flex items-center gap-3 sm:gap-4 bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-2xs">

                                        <div class="w-18 sm:w-20 aspect-[3/4] bg-slate-100 rounded-lg overflow-hidden shrink-0 shadow-xs">

                                            @if($request->offeredBook && $request->offeredBook->image)

                                                <img 
                                                    src="{{ asset('storage/' . $request->offeredBook->image) }}" 
                                                    alt="{{ $request->offeredBook->title }}" 
                                                    class="w-full h-full object-cover"
                                                >

                                            @else

                                                <div class="w-full h-full flex items-center justify-center text-2xl bg-indigo-50 text-indigo-400">
                                                    📖
                                                </div>

                                            @endif

                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <span class="inline-block px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                                                คุณเสนอเล่มนี้
                                            </span>

                                            <h4 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-2 leading-snug" title="{{ $request->offeredBook->title ?? '-' }}">
                                                {{ $request->offeredBook->title ?? 'หนังสือถูกลบ' }}
                                            </h4>

                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                {{ $request->offeredBook->author ?? 'ไม่ระบุผู้แต่ง' }}
                                            </p>

                                        </div>

                                    </div>

                                    {{-- Middle --}}
                                    <div class="flex flex-col items-center justify-center shrink-0 my-[-6px] md:my-0">

                                        <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-lg shadow-md shadow-amber-500/30">
                                            🔄
                                        </div>

                                        <span class="text-[10px] font-bold text-amber-700 uppercase tracking-widest mt-1">
                                            ขอแลกกับ
                                        </span>

                                    </div>

                                    {{-- Right --}}
                                    <div class="flex-1 w-full max-w-xs flex items-center gap-3 sm:gap-4 bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-2xs">

                                        <div class="w-18 sm:w-20 aspect-[3/4] bg-slate-100 rounded-lg overflow-hidden shrink-0 shadow-xs">

                                            @if($request->requestedBook && $request->requestedBook->image)

                                                <img 
                                                    src="{{ asset('storage/' . $request->requestedBook->image) }}" 
                                                    alt="{{ $request->requestedBook->title }}" 
                                                    class="w-full h-full object-cover"
                                                >

                                            @else

                                                <div class="w-full h-full flex items-center justify-center text-2xl bg-amber-50 text-amber-400">
                                                    📖
                                                </div>

                                            @endif

                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <span class="inline-block px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                                                หนังสือของเขาที่คุณอยากได้
                                            </span>

                                            <h4 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-2 leading-snug" title="{{ $request->requestedBook->title ?? '-' }}">
                                                {{ $request->requestedBook->title ?? 'หนังสือถูกลบ' }}
                                            </h4>

                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                {{ $request->requestedBook->author ?? 'ไม่ระบุผู้แต่ง' }}
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                {{-- ================================================= --}}
                                {{-- IN-APP CHAT BANNER : SENT / ACCEPTED OR COMPLETED --}}
                                {{-- ================================================= --}}
                                @if($request->status === 'accepted' || $request->status === 'completed')

                                    <div class="mt-5 p-5 bg-gradient-to-r from-indigo-50/90 via-purple-50/60 to-indigo-50/90 border border-indigo-200/90 rounded-2xl shadow-xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div class="flex items-center gap-3.5">
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center text-2xl shadow-md shadow-indigo-500/20 shrink-0">
                                                    💬
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-sm sm:text-base text-slate-800">
                                                        ห้องแชตประสานงาน BookCycle
                                                    </h4>
                                                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                                        คู่แลกเปลี่ยนยอมรับคำขอแล้ว เริ่มพูดคุย หรือส่งเลขพัสดุผ่านแชตของระบบได้ทันที
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('chats.start', $request->id) }}" 
                                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-500/20 transition-all">
                                                    <span>💬</span>
                                                    <span>เปิดห้องแชตพูดคุยทันที</span>
                                                    <span>→</span>
                                                </a>

                                                @php
                                                    $iConfirmed = ($request->requester_id === Auth::id() && $request->requester_confirmed_at) || ($request->receiver_id === Auth::id() && $request->receiver_confirmed_at);
                                                @endphp

                                                @if($request->status === 'completed')
                                                    <span class="px-3.5 py-2 bg-purple-100 text-purple-800 rounded-xl text-xs font-bold flex items-center gap-1">
                                                        <span>🎉</span> <span>แลกเปลี่ยนสำเร็จแล้ว</span>
                                                    </span>
                                                @elseif($iConfirmed)
                                                    <span class="px-3.5 py-2 bg-emerald-100 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-1">
                                                        <span>✓</span> <span>คุณยืนยันรับหนังสือแล้ว</span>
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('exchange-requests.confirm-received', $request->id) }}" class="inline" onsubmit="return confirm('ยืนยันว่าคุณได้รับหนังสือเรียบร้อยแล้ว?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                                            <span>📦</span>
                                                            <span>ยืนยันได้รับหนังสือแล้ว</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>



                                @endif

                            </div>

                        </div>

                    @endforeach

                @else

                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-500 shadow-xs">

                        <div class="text-5xl mb-3">
                            📤
                        </div>

                        <h3 class="font-bold text-base text-slate-700">
                            คุณยังไม่ได้ส่งคำขอแลกเปลี่ยน
                        </h3>

                        <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-sm mx-auto">
                            ไปยังหน้า "หนังสือที่ตรงกัน" เพื่อค้นหาและส่งคำขอแลกเปลี่ยนได้ทันที
                        </p>

                        <a 
                            href="{{ route('matching.index') }}" 
                            class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 shadow-sm"
                        >
                            🎯 ดูหนังสือที่ตรงกัน
                        </a>

                    </div>

                @endif

            </div>

            {{-- ========================================================= --}}
            {{-- 3. TAB: 3-WAY CIRCULAR TRADE RINGS --}}
            {{-- ========================================================= --}}
            <div x-show="activeTab === 'trade_rings'" x-cloak class="space-y-6">

                @if($tradeRings->count() > 0)

                    @foreach($tradeRings as $ring)
                        @php
                            $authId = auth()->id();
                            $myStatus = $ring->getUserStatus($authId);
                            $acceptedCount = $ring->acceptedCount();
                            $isRingAccepted = ($ring->status === 'accepted');
                            $isRingPending = ($ring->status === 'pending');
                            $isRingRejected = ($ring->status === 'rejected');

                            // หาว่าผู้ใช้อยู่ตำแหน่งใดในวงจร (1, 2, หรือ 3)
                            if ($authId == $ring->user1_id) {
                                $myGiveBook = $ring->book1;
                                $myReceiveBook = $ring->book3;
                                $partnerGiveTo = $ring->user2;
                                $partnerReceiveFrom = $ring->user3;
                            } elseif ($authId == $ring->user2_id) {
                                $myGiveBook = $ring->book2;
                                $myReceiveBook = $ring->book1;
                                $partnerGiveTo = $ring->user3;
                                $partnerReceiveFrom = $ring->user1;
                            } else {
                                $myGiveBook = $ring->book3;
                                $myReceiveBook = $ring->book2;
                                $partnerGiveTo = $ring->user1;
                                $partnerReceiveFrom = $ring->user2;
                            }
                        @endphp

                        <div class="bg-white rounded-3xl border-2 {{ $isRingAccepted ? 'border-emerald-300' : 'border-purple-200' }} shadow-sm hover:shadow-md transition-all overflow-hidden">
                            
                            {{-- Header --}}
                            <div class="px-5 py-4 {{ $isRingAccepted ? 'bg-gradient-to-r from-emerald-700 to-teal-800' : 'bg-gradient-to-r from-purple-800 via-indigo-800 to-purple-900' }} text-white flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-white/20 text-white border border-white/30 backdrop-blur-md">
                                        <span>🔄</span>
                                        <span>วงจรแลกเปลี่ยน 3 ฝ่าย #{{ $ring->id }}</span>
                                    </span>
                                    <span class="text-xs text-purple-200">
                                        (ริเริ่มโดย: <strong>{{ $ring->initiator->name ?? 'สมาชิก' }}</strong>)
                                    </span>
                                </div>

                                <div>
                                    @if($isRingAccepted)
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-emerald-400 text-emerald-950 shadow-sm">
                                            🎉 ยืนยันครบ 3/3 ฝ่าย (สำเร็จ)
                                        </span>
                                    @elseif($isRingPending)
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-amber-400 text-amber-950 shadow-sm">
                                            ⏳ ยืนยันแล้ว {{ $acceptedCount }}/3 ท่าน
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-red-400 text-red-950 shadow-sm">
                                            ❌ ถูกยกเลิกแล้ว
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-5 sm:p-7 space-y-6">

                                {{-- Circular 3-Step Overview --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                                    {{-- Step 1: User 1 -> User 2 --}}
                                    <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-200 text-xs space-y-2">
                                        <div class="flex items-center justify-between font-extrabold text-indigo-950">
                                            <span>1. {{ $ring->user1->name }} ➔ {{ $ring->user2->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full {{ $ring->user1_status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $ring->user1_status === 'accepted' ? '✅ ยืนยันแล้ว' : '⏳ รอยืนยัน' }}
                                            </span>
                                        </div>
                                        <p class="text-slate-800 font-bold truncate">📚 {{ $ring->book1->title ?? 'หนังสือ' }}</p>
                                        <p class="text-[11px] text-slate-500">เจ้าของ: {{ $ring->user1->name }}</p>
                                    </div>

                                    {{-- Step 2: User 2 -> User 3 --}}
                                    <div class="p-3.5 rounded-2xl bg-purple-50/70 border border-purple-200 text-xs space-y-2">
                                        <div class="flex items-center justify-between font-extrabold text-purple-950">
                                            <span>2. {{ $ring->user2->name }} ➔ {{ $ring->user3->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full {{ $ring->user2_status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $ring->user2_status === 'accepted' ? '✅ ยืนยันแล้ว' : '⏳ รอยืนยัน' }}
                                            </span>
                                        </div>
                                        <p class="text-slate-800 font-bold truncate">📚 {{ $ring->book2->title ?? 'หนังสือ' }}</p>
                                        <p class="text-[11px] text-slate-500">เจ้าของ: {{ $ring->user2->name }}</p>
                                    </div>

                                    {{-- Step 3: User 3 -> User 1 --}}
                                    <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs space-y-2">
                                        <div class="flex items-center justify-between font-extrabold text-amber-950">
                                            <span>3. {{ $ring->user3->name }} ➔ {{ $ring->user1->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full {{ $ring->user3_status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $ring->user3_status === 'accepted' ? '✅ ยืนยันแล้ว' : '⏳ รอยืนยัน' }}
                                            </span>
                                        </div>
                                        <p class="text-slate-800 font-bold truncate">📚 {{ $ring->book3->title ?? 'หนังสือ' }}</p>
                                        <p class="text-[11px] text-slate-500">เจ้าของ: {{ $ring->user3->name }}</p>
                                    </div>
                                </div>

                                {{-- What you give vs what you receive --}}
                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-16 bg-white rounded-lg border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if($myGiveBook && $myGiveBook->image)
                                                <img src="{{ asset('storage/' . $myGiveBook->image) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-lg">📖</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="text-[11px] font-extrabold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200">
                                                🎁 คุณส่งมอบให้ {{ $partnerGiveTo->name ?? 'สมาชิก' }}
                                            </span>
                                            <p class="text-xs sm:text-sm font-bold text-slate-900 truncate mt-1">{{ $myGiveBook->title ?? 'หนังสือ' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-16 bg-white rounded-lg border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if($myReceiveBook && $myReceiveBook->image)
                                                <img src="{{ asset('storage/' . $myReceiveBook->image) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-lg">✨</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="text-[11px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                ✨ คุณจะได้รับจาก {{ $partnerReceiveFrom->name ?? 'สมาชิก' }}
                                            </span>
                                            <p class="text-xs sm:text-sm font-bold text-slate-900 truncate mt-1">{{ $myReceiveBook->title ?? 'หนังสือ' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Confirmation Control Bar --}}
                                <div class="bg-slate-900 text-white p-4 sm:p-5 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div>
                                        <p class="text-xs sm:text-sm font-extrabold">
                                            @if($isRingAccepted)
                                                🎉 ยืนยันครบทั้ง 3 ฝ่ายแล้ว (สำเร็จ)
                                            @elseif($isRingPending)
                                                @if($myStatus === 'pending')
                                                    🔔 คุณยังไม่ได้ตอบรับข้อเสนอนี้
                                                @else
                                                    ⏳ คุณยืนยันแล้ว (กำลังรอสมาชิกอีก {{ 3 - $acceptedCount }} ท่าน)
                                                @endif
                                            @else
                                                ❌ ข้อเสนอนี้ถูกปฏิเสธหรือยกเลิกแล้ว
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            @if($isRingAccepted)
                                                หนังสือทั้ง 3 เล่มได้รับการแลกเปลี่ยนแล้ว ข้อมูลติดต่อถูกเปิดเผยด้านล่าง
                                            @elseif($isRingPending)
                                                ข้อมูลติดต่อจะเปิดเผยอัตโนมัติเมื่อครบ 3/3 คน
                                            @endif
                                        </p>
                                    </div>

                                    @if($isRingPending)
                                        @if($myStatus === 'pending')
                                            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                                                <form method="POST" action="{{ route('trade-rings.accept', $ring->id) }}" class="flex-1 sm:flex-none">
                                                    @csrf
                                                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 font-extrabold text-xs text-white shadow-md transition-all">
                                                        ✅ กดยืนยันยอมรับ
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('trade-rings.reject', $ring->id) }}" class="flex-1 sm:flex-none">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการปฏิเสธข้อเสนอนี้?')" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-red-900/60 border border-slate-700 font-bold text-xs text-slate-300 hover:text-red-200 transition-all">
                                                        ❌ ปฏิเสธ
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('trade-rings.reject', $ring->id) }}">
                                                @csrf
                                                <button type="submit" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกข้อเสนอนี้?')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-red-900/60 border border-slate-700 font-bold text-xs text-slate-400 hover:text-red-300 transition-all">
                                                    ❌ ยกเลิกข้อเสนอ
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>

                                {{-- Contact Information (LOCKED vs UNLOCKED) --}}
                                @if($isRingAccepted)
                                    <div class="p-4 sm:p-5 bg-emerald-50 rounded-2xl border border-emerald-200 space-y-3">
                                        <p class="text-xs sm:text-sm font-extrabold text-emerald-950 flex items-center gap-1.5">
                                            <span>🔓</span> <span>ข้อมูลติดต่อสมาชิกเพื่อประสานงานส่งมอบ:</span>
                                        </p>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            {{-- Partner 1 --}}
                                            <div class="p-3 bg-white rounded-xl border border-emerald-200 text-xs space-y-1.5">
                                                <p class="font-extrabold text-slate-900">{{ $partnerGiveTo->name }} (คนที่คุณส่งหนังสือให้)</p>
                                                <p class="text-slate-600">📞 โทร: <a href="tel:{{ $partnerGiveTo->phone }}" class="font-bold text-indigo-600 hover:underline">{{ $partnerGiveTo->phone ?? 'ไม่ระบุ' }}</a></p>
                                                <p class="text-slate-600">💬 Line ID: <span class="font-bold text-slate-900">{{ $partnerGiveTo->line_id ?? 'ไม่ระบุ' }}</span></p>
                                                <p class="text-slate-600">📍 พื้นที่: <span class="font-bold text-slate-900">{{ $partnerGiveTo->exchange_area ?? 'ไม่ระบุ' }}</span></p>
                                            </div>

                                            {{-- Partner 2 --}}
                                            <div class="p-3 bg-white rounded-xl border border-emerald-200 text-xs space-y-1.5">
                                                <p class="font-extrabold text-slate-900">{{ $partnerReceiveFrom->name }} (คนที่ส่งหนังสือให้คุณ)</p>
                                                <p class="text-slate-600">📞 โทร: <a href="tel:{{ $partnerReceiveFrom->phone }}" class="font-bold text-amber-600 hover:underline">{{ $partnerReceiveFrom->phone ?? 'ไม่ระบุ' }}</a></p>
                                                <p class="text-slate-600">💬 Line ID: <span class="font-bold text-slate-900">{{ $partnerReceiveFrom->line_id ?? 'ไม่ระบุ' }}</span></p>
                                                <p class="text-slate-600">📍 พื้นที่: <span class="font-bold text-slate-900">{{ $partnerReceiveFrom->exchange_area ?? 'ไม่ระบุ' }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-300 text-xs text-slate-600 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">🔒</span>
                                            <span>ข้อมูลติดต่อ (โทรศัพท์, LINE) ถูกซ่อนไว้และจะเปิดเผยเมื่อยืนยันครบทั้ง 3 ฝ่าย</span>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full bg-slate-200 font-bold text-[11px] text-slate-700 shrink-0">
                                            🔒 ล็อคเพื่อความปลอดภัย
                                        </span>
                                    </div>
                                @endif

                            </div>

                        </div>
                    @endforeach

                @else

                    <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-500 shadow-xs">
                        <div class="text-5xl mb-3">🔄</div>
                        <h3 class="font-bold text-base text-slate-700">ยังไม่มีคำขอแลกเปลี่ยนแบบวงจร 3 ฝ่าย</h3>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-sm mx-auto">
                            ระบบจะค้นหาวงจร 3 ฝ่ายให้อัตโนมัติในหน้า "หนังสือที่ตรงกัน"
                        </p>
                        <a href="{{ route('matching.index') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-purple-600 text-white text-xs font-semibold hover:bg-purple-700 shadow-sm">
                            🎯 ไปยังหน้าหนังสือที่ตรงกัน
                        </a>
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>