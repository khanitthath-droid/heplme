<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>🏠</span> 
                    <span>Dashboard ภาพรวมระบบ</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    ยินดีต้อนรับสู่ระบบศูนย์กลางการแลกเปลี่ยนหนังสือชุมชน
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('books.create') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                    <span>➕</span>
                    <span>ลงหนังสือของฉัน</span>
                </a>
                <a href="{{ route('wanted-books.create') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs sm:text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                    <span>🔎</span>
                    <span>ตามหาหนังสือ</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- 1. Hero Welcome Banner --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 text-white p-6 sm:p-8 shadow-xl">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold mb-3 border border-white/20">
                            <span>✨</span> <span>Book Exchange Platform v2.0</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                            สวัสดีคุณ {{ Auth::user()->name }} 👋
                        </h1>
                        <p class="text-indigo-100 text-sm sm:text-base mt-2 leading-relaxed">
                            เปลี่ยนหนังสือบนหิ้งที่คุณอ่านจบแล้ว ให้กลายเป็นหนังสือเล่มโปรดเล่มใหม่ แลกเปลี่ยนกับเพื่อนสมาชิกได้ง่าย รวดเร็ว และไม่มีค่าใช้จ่าย!
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                        <a href="{{ route('matching.index') }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-indigo-700 font-bold text-sm shadow-md hover:bg-indigo-50 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            <span>🎯</span>
                            <span>ดูหนังสือที่ตรงกัน</span>
                            @if($matchesCount > 0)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-500 text-white text-xs font-bold animate-pulse">
                                    {{ $matchesCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('exchange-requests.index') }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-semibold text-sm border border-white/20 transition-all">
                            <span>🔄</span>
                            <span>จัดการคำขอแลกเปลี่ยน</span>
                            @if($receivedPendingCount > 0)
                                <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-900 text-xs font-bold">
                                    {{ $receivedPendingCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Decorative blur blobs -->
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-purple-500/30 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-10 -top-10 w-64 h-64 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            {{-- 2. Personal & Platform Statistics Grid --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span>📊</span> <span>สถิติของคุณและภาพรวมระบบ</span>
                    </h3>
                    <span class="text-xs text-slate-500">อัปเดตข้อมูลแบบเรียลไทม์</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    {{-- Card 1: หนังสือของฉัน --}}
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">หนังสือของฉัน</p>
                                <h4 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $myBooksCount }}</h4>
                                <p class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1">
                                    <span>🟢</span> พร้อมแลก {{ $myAvailableBooksCount }} เล่ม
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                📚
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                            <a href="{{ route('books.index') }}" class="text-indigo-600 font-semibold hover:text-indigo-800 flex items-center gap-1">
                                ดูทั้งหมด <span>→</span>
                            </a>
                            <a href="{{ route('books.create') }}" class="text-slate-400 hover:text-slate-600 font-medium">
                                + เพิ่มเล่มใหม่
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: หนังสือที่ตามหา --}}
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">หนังสือที่ต้องการ</p>
                                <h4 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $myWantedBooksCount }}</h4>
                                <p class="text-xs text-amber-600 font-medium mt-1 flex items-center gap-1">
                                    <span>📌</span> รายการที่คุณอยากได้
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                🔎
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                            <a href="{{ route('wanted-books.index') }}" class="text-amber-600 font-semibold hover:text-amber-800 flex items-center gap-1">
                                ดูรายการที่ตามหา <span>→</span>
                            </a>
                            <a href="{{ route('wanted-books.create') }}" class="text-slate-400 hover:text-slate-600 font-medium">
                                + เพิ่มรายการ
                            </a>
                        </div>
                    </div>

                    {{-- Card 3: คำขอแลกเปลี่ยน --}}
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">คำขอรอดำเนินการ</p>
                                <h4 class="text-3xl font-extrabold text-slate-800 mt-2">
                                    {{ $receivedPendingCount + $sentPendingCount }}
                                </h4>
                                <p class="text-xs text-purple-600 font-medium mt-1">
                                    ได้รับ <span class="font-bold">{{ $receivedPendingCount }}</span> | ส่งไป <span class="font-bold">{{ $sentPendingCount }}</span>
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                🔄
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                            <a href="{{ route('exchange-requests.index') }}" class="text-purple-600 font-semibold hover:text-purple-800 flex items-center gap-1">
                                จัดการคำขอ <span>→</span>
                            </a>
                            <span class="text-emerald-600 font-medium">
                                สำเร็จแล้ว {{ $successfulExchangesCount }} รายการ
                            </span>
                        </div>
                    </div>

                    {{-- Card 4: คู่ที่ตรงกัน (Matching) --}}
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group {{ $matchesCount > 0 ? 'ring-2 ring-emerald-500/20' : '' }}">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">คู่ที่ตรงกัน (Match)</p>
                                <h4 class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $matchesCount }}</h4>
                                <p class="text-xs text-slate-500 font-medium mt-1">
                                    @if($matchesCount > 0)
                                        <span>✨ 2-Way: {{ $twoWayMatchesCount ?? 0 }} | ลูกโซ่ 3-Way: {{ $threeWayMatchesCount ?? 0 }}</span>
                                    @else
                                        <span>ยังไม่พบคู่ที่ตรงกัน</span>
                                    @endif
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                🎯
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                            <a href="{{ route('matching.index') }}" class="text-emerald-600 font-bold hover:text-emerald-800 flex items-center gap-1">
                                ตรวจสอบคู่แลกเปลี่ยน <span>→</span>
                            </a>
                            <span class="text-slate-400">ระบบ AI จับคู่</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 3. System Highlights Bar --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 gap-4 md:gap-0">
                    <div class="flex items-center gap-4 px-3 py-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shrink-0">
                            👥
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">สมาชิกในคอมมูนิตี้</div>
                            <div class="text-lg font-bold text-slate-800">{{ $totalPlatformUsers }} คน</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 px-3 py-2 md:pl-6">
                        <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-xl shrink-0">
                            📖
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">หนังสือในคลังระบบ</div>
                            <div class="text-lg font-bold text-slate-800">{{ $totalPlatformBooks }} เล่ม</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 px-3 py-2 md:pl-6">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl shrink-0">
                            🤝
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">การแลกเปลี่ยนที่สำเร็จ</div>
                            <div class="text-lg font-bold text-slate-800">{{ $totalPlatformExchanges }} ครั้ง</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Community Available Books Showcase --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span>📚</span> <span>หนังสือใหม่จากเพื่อนสมาชิก</span>
                        </h3>
                        <p class="text-xs text-slate-500">หนังสือที่สมาชิกคนอื่นลงประกาศไว้และพร้อมแลกเปลี่ยน</p>
                    </div>
                    <a href="{{ route('matching.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        ค้นหาคู่ที่ตรงกัน <span>→</span>
                    </a>
                </div>

                @if($communityBooks->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach($communityBooks as $cBook)
                            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col group">
                                {{-- Book Cover --}}
                                <div class="relative aspect-[3/4] bg-slate-100 overflow-hidden">
                                    @if($cBook->image)
                                        <img src="{{ asset('storage/' . $cBook->image) }}" 
                                             alt="{{ $cBook->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-slate-100 text-slate-400">
                                            <span class="text-3xl">📖</span>
                                            <span class="text-[10px] text-slate-400 mt-1 font-medium">ไม่มีรูปปก</span>
                                        </div>
                                    @endif

                                    @if($cBook->condition)
                                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-xs text-white text-[10px] font-medium">
                                            {{ $cBook->condition }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="p-3 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h5 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-1 leading-snug" title="{{ $cBook->title }}">
                                            {{ $cBook->title }}
                                        </h5>
                                        <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                            {{ $cBook->author ?? 'ไม่ระบุผู้แต่ง' }}
                                        </p>
                                    </div>

                                    <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500 truncate max-w-[80px]">
                                            👤 {{ $cBook->user->name ?? 'สมาชิก' }}
                                        </span>
                                        <span class="text-emerald-600 font-bold">
                                            พร้อมแลก
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center text-slate-500">
                        <div class="text-4xl mb-2">📚</div>
                        <p class="font-medium text-sm">ยังไม่มีหนังสือจากเพื่อนสมาชิก</p>
                        <p class="text-xs text-slate-400 mt-1">มาร่วมเป็นคนแรกที่ลงประกาศแลกหนังสือกันเถอะ</p>
                    </div>
                @endif
            </div>

            {{-- 5. Recent Exchange Requests Activity --}}
            @if($recentRequests->count() > 0)
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span>🕒</span> <span>กิจกรรมคำขอล่าสุดของคุณ</span>
                        </h3>
                        <a href="{{ route('exchange-requests.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            ดูคำขอทั้งหมด <span>→</span>
                        </a>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 divide-y divide-slate-100 shadow-xs overflow-hidden">
                        @foreach($recentRequests as $req)
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/60 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full {{ $req->requester_id === Auth::id() ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-sm font-bold shrink-0">
                                        {{ $req->requester_id === Auth::id() ? '📤' : '📥' }}
                                    </div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-semibold text-slate-800">
                                            @if($req->requester_id === Auth::id())
                                                คุณส่งคำขอแลกเปลี่ยนไปยัง <span class="text-indigo-600">{{ $req->receiver->name ?? 'สมาชิก' }}</span>
                                            @else
                                                <span class="text-indigo-600">{{ $req->requester->name ?? 'สมาชิก' }}</span> ส่งคำขอแลกเปลี่ยนมายังคุณ
                                            @endif
                                        </p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            เสนอ: {{ $req->offeredBook->title ?? '-' }} ⇄ ต้องการ: {{ $req->requestedBook->title ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 self-end sm:self-center">
                                    @if($req->status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            ⏳ รอดำเนินการ
                                        </span>
                                    @elseif($req->status === 'accepted')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            ✅ แลกเปลี่ยนสำเร็จ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            ❌ ปฏิเสธแล้ว
                                        </span>
                                    @endif

                                    <a href="{{ route('exchange-requests.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 px-2 py-1 rounded-lg hover:bg-indigo-50">
                                        ดูรายละเอียด
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
