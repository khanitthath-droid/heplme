<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>💬</span>
                    <span>ระบบแชตแลกเปลี่ยนหนังสือ</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    พูดคุย ประสานงาน ส่งรูปภาพ และนัดหมายส่งมอบหนังสืออย่างปลอดภัยภายใน BookCycle
                </p>
            </div>

            <a href="{{ route('exchange-requests.index') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs sm:text-sm font-semibold transition-all self-start sm:self-auto">
                <span>←</span>
                <span>กลับหน้ารายการคำขอ</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8" 
         x-data="chatApp({
             activeRoomId: {{ $activeRoom ? $activeRoom->id : 'null' }},
             currentUserId: {{ Auth::id() }},
             initialMessages: {{ json_encode($messages->map(function($m) {
                 return [
                     'id' => $m->id,
                     'sender_id' => $m->sender_id,
                     'sender_name' => $m->sender ? $m->sender->name : 'สมาชิก',
                     'sender_avatar' => $m->sender && $m->sender->avatar ? asset('storage/' . $m->sender->avatar) : null,
                     'is_me' => $m->sender_id === Auth::id(),
                     'message' => $m->deleted_by_sender ? 'ข้อความนี้ถูกยกเลิกแล้ว' : $m->message,
                     'type' => $m->type,
                     'image_url' => (!$m->deleted_by_sender && $m->image_path) ? asset('storage/' . $m->image_path) : null,
                     'metadata' => $m->metadata,
                     'is_read' => $m->is_read,
                     'deleted_by_sender' => $m->deleted_by_sender,
                     'time' => $m->created_at->locale('th')->translatedFormat('H:i น.'),
                     'date' => $m->created_at->locale('th')->translatedFormat('j M Y'),
                 ];
             })) }},
             fetchUrl: '{{ $activeRoom ? route('chats.fetch', $activeRoom->id) : '' }}',
             sendUrl: '{{ $activeRoom ? route('chats.send', $activeRoom->id) : '' }}',
             structuredUrl: '{{ $activeRoom ? route('chats.structured', $activeRoom->id) : '' }}',
             typingUrl: '{{ $activeRoom ? route('chats.typing', $activeRoom->id) : '' }}',
             isBlocked: {{ $isBlocked ? 'true' : 'false' }},
             counterpartOnline: {{ $counterpart && $counterpart->isOnline() ? 'true' : 'false' }},
             csrfToken: '{{ csrf_token() }}'
         })"
         x-init="initChat()">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
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

            @if(session('error'))
                <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <p class="font-bold text-sm">แจ้งเตือน</p>
                            <p class="text-xs text-red-700 mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700 text-lg p-1">✕</button>
                </div>
            @endif

            {{-- Main Chat Container --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col md:flex-row h-[780px]">

                {{-- ========================================================= --}}
                {{-- LEFT PANE: CONVERSATION LIST (รายชื่อห้องแชต) --}}
                {{-- ========================================================= --}}
                <div class="w-full md:w-80 lg:w-96 border-r border-slate-100 flex flex-col shrink-0 bg-slate-50/50"
                     :class="activeRoomId && mobileShowChat ? 'hidden md:flex' : 'flex'">

                    {{-- Search / Filter Header --}}
                    <div class="p-4 border-b border-slate-100 bg-white">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-base text-slate-800 flex items-center gap-2">
                                <span>📬</span>
                                <span>กล่องข้อความ</span>
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                {{ $chatRooms->count() }} ห้อง
                            </span>
                        </div>

                        <div class="relative">
                            <input 
                                type="text" 
                                x-model="searchQuery" 
                                placeholder="ค้นหาคู่สนทนา หรือชื่อหนังสือ..." 
                                class="w-full pl-9 pr-4 py-2 bg-slate-100/70 border-none rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all"
                            >
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400">🔍</span>
                        </div>
                    </div>

                    {{-- Rooms List --}}
                    <div class="flex-1 overflow-y-auto p-3 space-y-1.5 divide-y divide-slate-100/60">
                        @forelse($chatRooms as $room)
                            @php
                                $other = $room->otherUser(Auth::id());
                                $latest = $room->latestMessage;
                                $req = $room->exchangeRequest;
                                $unread = $room->unreadCountFor(Auth::id());
                                $isActive = $activeRoom && $activeRoom->id === $room->id;
                            @endphp

                            <a href="{{ route('chats.show', $room->id) }}"
                               @click="mobileShowChat = true"
                               x-show="matchesSearch('{{ addslashes($other->name) }}', '{{ addslashes($req->offeredBook->title ?? '') }}', '{{ addslashes($req->requestedBook->title ?? '') }}')"
                               class="group block p-3 rounded-2xl transition-all duration-150 {{ $isActive ? 'bg-white shadow-xs border border-indigo-100 ring-2 ring-indigo-500/10' : 'hover:bg-white/80 hover:shadow-2xs' }}">
                                
                                <div class="flex items-start gap-3">
                                    {{-- Avatar with Online Indicator --}}
                                    <div class="relative shrink-0">
                                        @if($other && $other->avatar)
                                            <img src="{{ asset('storage/' . $other->avatar) }}" alt="{{ $other->name }}" class="w-11 h-11 rounded-full object-cover ring-2 ring-white shadow-xs">
                                        @else
                                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm flex items-center justify-center shadow-xs">
                                                {{ mb_substr($other->name ?? '?', 0, 1) }}
                                            </div>
                                        @endif

                                        {{-- Online / Offline dot --}}
                                        <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full border-2 border-white {{ $other && $other->isOnline() ? 'bg-emerald-500' : 'bg-slate-300' }}"
                                              title="{{ $other && $other->isOnline() ? 'ออนไลน์' : 'ออฟไลน์' }}">
                                        </span>
                                    </div>

                                    {{-- Info & snippet --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1 mb-0.5">
                                            <h4 class="font-bold text-xs sm:text-sm text-slate-800 truncate group-hover:text-indigo-600 transition-colors">
                                                {{ $other->name ?? 'คู่แลกเปลี่ยน' }}
                                            </h4>
                                            @if($room->last_message_at)
                                                <span class="text-[10px] text-slate-400 shrink-0">
                                                    {{ $room->last_message_at->locale('th')->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Exchange Book info badge --}}
                                        @if($req)
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 truncate mb-1">
                                                <span class="text-xs">📖</span>
                                                <span class="truncate font-medium text-slate-600">
                                                    {{ $req->offeredBook->title ?? 'หนังสือ' }} ⇄ {{ $req->requestedBook->title ?? 'หนังสือ' }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Last message snippet --}}
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs text-slate-500 truncate">
                                                @if($latest)
                                                    @if($latest->type === 'image')
                                                        <span>📷 ส่งรูปภาพ</span>
                                                    @elseif($latest->type === 'meetup')
                                                        <span>📅 นัดหมายรับหนังสือ</span>
                                                    @elseif($latest->type === 'delivery')
                                                        <span>📦 แจ้งเลขพัสดุ</span>
                                                    @elseif($latest->type === 'system')
                                                        <span class="text-indigo-600 font-semibold">{{ $latest->message }}</span>
                                                    @else
                                                        {{ $latest->deleted_by_sender ? 'ข้อความถูกยกเลิก' : $latest->message }}
                                                    @endif
                                                @else
                                                    <span class="italic text-slate-400">ยังไม่มีข้อความ</span>
                                                @endif
                                            </p>

                                            {{-- Unread Badge --}}
                                            @if($unread > 0)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white shrink-0 shadow-xs">
                                                    {{ $unread }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-12 px-4">
                                <span class="text-4xl mb-3 block">📭</span>
                                <h4 class="font-bold text-sm text-slate-700">ยังไม่มีห้องแชต</h4>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                    เมื่อคุณส่งหรือได้รับคำขอแลกเปลี่ยนหนังสือ สามารถเริ่มแชตเพื่อพูดคุยได้ทันที
                                </p>
                                <a href="{{ route('exchange-requests.index') }}" class="mt-4 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs transition-colors">
                                    <span>ดูรายการคำขอ</span> <span>→</span>
                                </a>
                            </div>
                        @endforelse
                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- RIGHT PANE: ACTIVE CHAT CONVERSATION --}}
                {{-- ========================================================= --}}
                <div class="flex-1 flex flex-col bg-white overflow-hidden"
                     :class="!mobileShowChat && 'hidden md:flex'">

                    @if($activeRoom)
                        @php
                            $req = $activeRoom->exchangeRequest;
                        @endphp

                        {{-- Active Chat Header --}}
                        <div class="px-4 py-3 border-b border-slate-100 bg-white/90 backdrop-blur-md flex items-center justify-between gap-3 shrink-0">
                            
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Back button for mobile --}}
                                <button type="button" 
                                        @click="mobileShowChat = false" 
                                        class="md:hidden p-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-bold">
                                    ←
                                </button>

                                {{-- Counterpart Avatar --}}
                                <div class="relative shrink-0">
                                    @if($counterpart && $counterpart->avatar)
                                        <img src="{{ asset('storage/' . $counterpart->avatar) }}" alt="{{ $counterpart->name }}" class="w-10 h-10 rounded-full object-cover ring-1 ring-slate-200 shadow-xs">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm flex items-center justify-center shadow-xs">
                                            {{ mb_substr($counterpart->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif

                                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white"
                                          :class="counterpartOnline ? 'bg-emerald-500' : 'bg-slate-300'">
                                    </span>
                                </div>

                                <div class="min-w-0">
                                    <h3 class="font-bold text-sm text-slate-800 truncate">
                                        {{ $counterpart->name ?? 'สมาชิก' }}
                                    </h3>
                                    <p class="text-[11px] flex items-center gap-1.5"
                                       :class="counterpartOnline ? 'text-emerald-600 font-semibold' : 'text-slate-400'">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="counterpartOnline ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300'"></span>
                                        <span x-text="counterpartOnline ? 'ออนไลน์ในระบบ' : counterpartLastSeen"></span>
                                    </p>
                                </div>
                            </div>

                            {{-- Chat Actions Dropdown --}}
                            <div class="flex items-center gap-2">
                                
                                {{-- Actions 3-Dots Menu --}}
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" type="button" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>

                                    <div x-show="open" 
                                         @click.away="open = false" 
                                         x-cloak
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-30 text-xs space-y-1">
                                        
                                        {{-- Report Modal Trigger --}}
                                        <button type="button" 
                                                @click="open = false; showReportModal = true; reportTarget = { type: 'user', id: {{ $counterpart->id }} }"
                                                class="w-full px-4 py-2 text-left text-slate-700 hover:bg-red-50 hover:text-red-600 flex items-center gap-2">
                                            <span>⚠️</span>
                                            <span>รายงานผู้ใช้นี้</span>
                                        </button>

                                        {{-- Block / Unblock --}}
                                        @if(!$isBlocked)
                                            <form method="POST" action="{{ route('user-blocks.store') }}" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการบล็อกผู้ใช้นี้?');">
                                                @csrf
                                                <input type="hidden" name="blocked_user_id" value="{{ $counterpart->id }}">
                                                <button type="submit" class="w-full px-4 py-2 text-left text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                    <span>🚫</span>
                                                    <span>บล็อกผู้ใช้นี้</span>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('user-blocks.destroy', $counterpart->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-4 py-2 text-left text-emerald-600 hover:bg-emerald-50 flex items-center gap-2">
                                                    <span>🔓</span>
                                                    <span>ปลดบล็อกผู้ใช้</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================= --}}
                        {{-- EXCHANGE SUMMARY BAR (แสดงหนังสือและปุ่มจัดการคำขอในแชต) --}}
                        {{-- ========================================================= --}}
                        @if($req)
                            <div class="px-4 py-2.5 bg-gradient-to-r from-indigo-50/70 via-purple-50/50 to-indigo-50/70 border-b border-indigo-100/80 flex flex-wrap items-center justify-between gap-3 shrink-0">
                                
                                {{-- Book comparison preview --}}
                                <div class="flex items-center gap-3">
                                    {{-- Book 1 --}}
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-10 bg-slate-200 rounded overflow-hidden shadow-2xs shrink-0">
                                            @if($req->offeredBook && $req->offeredBook->image)
                                                <img src="{{ asset('storage/' . $req->offeredBook->image) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs">📖</div>
                                            @endif
                                        </div>
                                        <div class="text-[11px] leading-tight max-w-[120px] sm:max-w-[150px]">
                                            <p class="font-bold text-slate-800 truncate" title="{{ $req->offeredBook->title ?? '' }}">
                                                {{ $req->offeredBook->title ?? 'หนังสือ' }}
                                            </p>
                                            <span class="text-[10px] text-indigo-600 font-medium">
                                                {{ $req->requester_id === Auth::id() ? '(ของคุณ)' : '(ของเขา)' }}
                                            </span>
                                        </div>
                                    </div>

                                    <span class="text-sm font-bold text-indigo-400">⇄</span>

                                    {{-- Book 2 --}}
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-10 bg-slate-200 rounded overflow-hidden shadow-2xs shrink-0">
                                            @if($req->requestedBook && $req->requestedBook->image)
                                                <img src="{{ asset('storage/' . $req->requestedBook->image) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs">📖</div>
                                            @endif
                                        </div>
                                        <div class="text-[11px] leading-tight max-w-[120px] sm:max-w-[150px]">
                                            <p class="font-bold text-slate-800 truncate" title="{{ $req->requestedBook->title ?? '' }}">
                                                {{ $req->requestedBook->title ?? 'หนังสือ' }}
                                            </p>
                                            <span class="text-[10px] text-purple-600 font-medium">
                                                {{ $req->receiver_id === Auth::id() ? '(ของคุณ)' : '(ของเขา)' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Exchange Status & Actions --}}
                                <div class="flex items-center gap-2">
                                    
                                    {{-- Status Badge --}}
                                    @if($req->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            ⏳ รอตอบรับ
                                        </span>

                                        {{-- If current user is receiver, show accept/reject buttons right inside chat! --}}
                                        @if($req->receiver_id === Auth::id())
                                            <form method="POST" action="{{ route('exchange-requests.accept', $req->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition-colors">
                                                    ✅ ยอมรับคำขอ
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('exchange-requests.reject', $req->id) }}" class="inline" onsubmit="return confirm('ต้องการปฏิเสธคำขอนี้หรือไม่?');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-slate-200 hover:bg-red-100 hover:text-red-700 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                                                    ✕ ปฏิเสธ
                                                </button>
                                            </form>
                                        @endif

                                    @elseif($req->status === 'accepted')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            🤝 ยอมรับแล้ว
                                        </span>

                                        {{-- Confirm Received Button --}}
                                        @php
                                            $iConfirmed = ($req->requester_id === Auth::id() && $req->requester_confirmed_at) || ($req->receiver_id === Auth::id() && $req->receiver_confirmed_at);
                                        @endphp

                                        @if($iConfirmed)
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold flex items-center gap-1">
                                                <span>✓</span> <span>คุณยืนยันรับของแล้ว</span>
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('exchange-requests.confirm-received', $req->id) }}" class="inline" onsubmit="return confirm('ยืนยันว่าคุณได้รับหนังสือเรียบร้อยแล้วใช่หรือไม่?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-xs transition-colors flex items-center gap-1">
                                                    <span>📦</span> <span>ยืนยันได้รับหนังสือแล้ว</span>
                                                </button>
                                            </form>
                                        @endif

                                    @elseif($req->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                            <span>🎉</span>
                                            <span>แลกเปลี่ยนสำเร็จ</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                            <span>❌</span>
                                            <span>ปฏิเสธคำขอแล้ว</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- ========================================================= --}}
                        {{-- MESSAGE STREAM (ประวัติและการแสดงผลข้อความ) --}}
                        {{-- ========================================================= --}}
                        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-slate-50/40" id="messagesContainer">
                            
                            <template x-for="(msg, index) in messages" :key="msg.id">
                                <div>
                                    {{-- Date separator --}}
                                    <template x-if="index === 0 || messages[index - 1].date !== msg.date">
                                        <div class="flex items-center justify-center my-3">
                                            <span class="px-3 py-1 rounded-full bg-slate-200/70 text-slate-600 text-[10px] font-bold" x-text="msg.date"></span>
                                        </div>
                                    </template>

                                    {{-- SYSTEM MESSAGE --}}
                                    <template x-if="msg.type === 'system'">
                                        <div class="flex justify-center my-2">
                                            <div class="max-w-md px-4 py-2 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-900 text-xs text-center shadow-2xs font-medium leading-relaxed">
                                                <span x-text="msg.message"></span>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- USER MESSAGE (LEFT: COUNTERPART / RIGHT: ME) --}}
                                    <template x-if="msg.type !== 'system'">
                                        <div class="flex items-end gap-2 group" :class="msg.is_me ? 'justify-end' : 'justify-start'">
                                            
                                            {{-- Counterpart Avatar on left --}}
                                            <template x-if="!msg.is_me">
                                                <div class="w-7 h-7 rounded-full bg-slate-200 shrink-0 overflow-hidden mb-1">
                                                    <template x-if="msg.sender_avatar">
                                                        <img :src="msg.sender_avatar" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!msg.sender_avatar">
                                                        <div class="w-full h-full bg-indigo-500 text-white text-[10px] font-bold flex items-center justify-center" x-text="msg.sender_name.charAt(0)"></div>
                                                    </template>
                                                </div>
                                            </template>

                                            {{-- Delete Button for My Message --}}
                                            <template x-if="msg.is_me && !msg.deleted_by_sender">
                                                <button type="button" 
                                                        @click="deleteMsg(msg.id)"
                                                        class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-500 text-xs p-1 transition-opacity" 
                                                        title="ลบข้อความ">
                                                    🗑️
                                                </button>
                                            </template>

                                            {{-- Bubble Box --}}
                                            <div class="max-w-xs sm:max-w-md rounded-2xl p-3 sm:p-3.5 shadow-2xs"
                                                 :class="msg.is_me ? 'bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white rounded-br-xs' : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs'">
                                                
                                                {{-- Name if counterpart --}}
                                                <template x-if="!msg.is_me">
                                                    <p class="text-[10px] font-bold text-indigo-600 mb-1" x-text="msg.sender_name"></p>
                                                </template>

                                                {{-- 1. DELETED MESSAGE --}}
                                                <template x-if="msg.deleted_by_sender">
                                                    <p class="text-xs italic" :class="msg.is_me ? 'text-indigo-200' : 'text-slate-400'">
                                                        🚫 ข้อความนี้ถูกยกเลิกแล้ว
                                                    </p>
                                                </template>

                                                {{-- 2. TEXT MESSAGE --}}
                                                <template x-if="!msg.deleted_by_sender && msg.type === 'text'">
                                                    <p class="text-xs sm:text-sm whitespace-pre-wrap leading-relaxed break-words" x-text="msg.message"></p>
                                                </template>

                                                {{-- 3. IMAGE MESSAGE --}}
                                                <template x-if="!msg.deleted_by_sender && msg.type === 'image'">
                                                    <div>
                                                        <template x-if="msg.image_url">
                                                            <img :src="msg.image_url" 
                                                                 @click="openLightbox(msg.image_url)" 
                                                                 class="rounded-xl max-h-64 object-cover cursor-pointer hover:opacity-95 transition-opacity shadow-xs mb-1.5">
                                                        </template>
                                                        <template x-if="msg.message">
                                                            <p class="text-xs sm:text-sm whitespace-pre-wrap leading-relaxed break-words" x-text="msg.message"></p>
                                                        </template>
                                                    </div>
                                                </template>

                                                {{-- 4. STRUCTURED MEETUP CARD --}}
                                                <template x-if="!msg.deleted_by_sender && msg.type === 'meetup'">
                                                    <div class="p-3 rounded-xl" :class="msg.is_me ? 'bg-white/10 text-white border border-white/20' : 'bg-indigo-50/80 text-slate-800 border border-indigo-100'">
                                                        <div class="flex items-center gap-2 font-bold text-xs mb-2">
                                                            <span class="text-lg">📅</span>
                                                            <span>ใบนัดหมายรับหนังสือ</span>
                                                        </div>
                                                        <div class="space-y-1 text-xs">
                                                            <p><span class="font-semibold opacity-75">สถานที่:</span> <span x-text="msg.metadata?.location"></span></p>
                                                            <p><span class="font-semibold opacity-75">วันที่:</span> <span x-text="msg.metadata?.date"></span></p>
                                                            <p><span class="font-semibold opacity-75">เวลา:</span> <span x-text="msg.metadata?.time"></span></p>
                                                            <template x-if="msg.metadata?.notes">
                                                                <p><span class="font-semibold opacity-75">หมายเหตุ:</span> <span x-text="msg.metadata?.notes"></span></p>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- 5. STRUCTURED DELIVERY CARD --}}
                                                <template x-if="!msg.deleted_by_sender && msg.type === 'delivery'">
                                                    <div class="p-3 rounded-xl" :class="msg.is_me ? 'bg-white/10 text-white border border-white/20' : 'bg-emerald-50/80 text-slate-800 border border-emerald-100'">
                                                        <div class="flex items-center gap-2 font-bold text-xs mb-2">
                                                            <span class="text-lg">🚚</span>
                                                            <span>รายละเอียดจัดส่งพัสดุ</span>
                                                        </div>
                                                        <div class="space-y-1 text-xs">
                                                            <p><span class="font-semibold opacity-75">ขนส่ง:</span> <span x-text="msg.metadata?.carrier"></span></p>
                                                            <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg" :class="msg.is_me ? 'bg-white/20' : 'bg-white'">
                                                                <span class="font-mono font-bold" x-text="msg.metadata?.tracking_number"></span>
                                                                <button type="button" @click="navigator.clipboard.writeText(msg.metadata?.tracking_number); alert('คัดลอกเลขพัสดุแล้ว');" class="text-[10px] px-2 py-0.5 rounded bg-indigo-600 text-white font-bold">คัดลอก</button>
                                                            </div>
                                                            <template x-if="msg.metadata?.notes">
                                                                <p class="mt-1"><span class="font-semibold opacity-75">หมายเหตุ:</span> <span x-text="msg.metadata?.notes"></span></p>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- Timestamp & Read Status --}}
                                                <div class="flex items-center justify-end gap-1 mt-1 text-[10px]"
                                                     :class="msg.is_me ? 'text-indigo-200' : 'text-slate-400'">
                                                    <span x-text="msg.time"></span>
                                                    <template x-if="msg.is_me">
                                                        <span x-text="msg.is_read ? '✓✓ อ่านแล้ว' : '✓'"></span>
                                                    </template>
                                                </div>

                                            </div>

                                            {{-- Report button for counterpart message --}}
                                            <template x-if="!msg.is_me">
                                                <button type="button" 
                                                        @click="showReportModal = true; reportTarget = { type: 'message', id: msg.id, userId: msg.sender_id }"
                                                        class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-amber-600 text-xs p-1 transition-opacity" 
                                                        title="รายงานข้อความนี้">
                                                    ⚠️
                                                </button>
                                            </template>

                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Typing indicator bubble --}}
                            <div x-show="isTyping" x-cloak class="flex items-center gap-2 text-xs text-slate-500 py-1">
                                <div class="px-3.5 py-2 bg-white rounded-2xl rounded-bl-xs border border-slate-200 shadow-2xs flex items-center gap-1.5">
                                    <span class="font-semibold text-[11px] text-indigo-600" x-text="counterpartName + ' กำลังพิมพ์'"></span>
                                    <div class="flex gap-1">
                                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- ========================================================= --}}
                        {{-- CHAT INPUT FOOTER (ช่องพิมพ์และแนบรูปภาพ/การ์ด) --}}
                        {{-- ========================================================= --}}
                        <div class="p-3 sm:p-4 border-t border-slate-100 bg-white shrink-0">
                            
                            {{-- If user is blocked --}}
                            <template x-if="isBlocked">
                                <div class="p-3 bg-red-50 text-red-700 rounded-2xl border border-red-200 text-center text-xs font-semibold">
                                    🚫 คุณไม่สามารถส่งข้อความได้เนื่องจากมีการบล็อกผู้ใช้ในรายการนี้
                                </div>
                            </template>

                            <template x-if="!isBlocked">
                                <div class="space-y-2">
                                    
                                    {{-- Quick reply chips --}}
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px] scrollbar-none">
                                        <button type="button" @click="textInput = 'ขอรูปภาพมุมเพิ่มเติมหน่อยครับ'; $refs.msgInput.focus();" class="shrink-0 px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                            📷 ขอรูปมุมเพิ่มเติม
                                        </button>
                                        <button type="button" @click="textInput = 'หนังสือมีรอยขีดเขียนหรือชำรุดตรงไหนไหมครับ'; $refs.msgInput.focus();" class="shrink-0 px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                            📖 สภาพมีรอยไหม
                                        </button>
                                        <button type="button" @click="textInput = 'สะดวกนัดรับแถวไหนดีครับ'; $refs.msgInput.focus();" class="shrink-0 px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                            📍 สะดวกนัดรับที่ไหน
                                        </button>
                                        <button type="button" @click="showMeetupModal = true" class="shrink-0 px-2.5 py-1 rounded-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold transition-colors">
                                            📅 เสนอนัดรับ
                                        </button>
                                        <button type="button" @click="showDeliveryModal = true" class="shrink-0 px-2.5 py-1 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition-colors">
                                            📦 แจ้งเลขพัสดุ
                                        </button>
                                    </div>

                                    {{-- Image Preview if selected --}}
                                    <template x-if="imagePreview">
                                        <div class="flex items-center gap-3 p-2 bg-slate-100 rounded-xl max-w-xs">
                                            <img :src="imagePreview" class="w-12 h-12 rounded-lg object-cover">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-semibold text-slate-700 truncate" x-text="imageFile?.name"></p>
                                                <p class="text-[10px] text-slate-400">รูปภาพพร้อมส่ง</p>
                                            </div>
                                            <button type="button" @click="clearImage()" class="text-red-500 hover:text-red-700 text-sm p-1">✕</button>
                                        </div>
                                    </template>

                                    {{-- Input form bar --}}
                                    <form @submit.prevent="sendTextMessage()" class="flex items-center gap-2">
                                        
                                        {{-- Image upload trigger --}}
                                        <label class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-indigo-600 cursor-pointer transition-colors shrink-0" title="ส่งรูปภาพ">
                                            <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" @change="handleImageSelect($event)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </label>

                                        {{-- Text Input --}}
                                        <input 
                                            type="text" 
                                            x-ref="msgInput"
                                            x-model="textInput" 
                                            @keyup="handleKeyup()"
                                            placeholder="พิมพ์ข้อความ... (กด Enter เพื่อส่ง)" 
                                            class="flex-1 bg-slate-100/70 border-none rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all"
                                        >

                                        {{-- Send Button --}}
                                        <button 
                                            type="submit" 
                                            :disabled="!textInput.trim() && !imageFile"
                                            :class="(!textInput.trim() && !imageFile) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700 active:scale-95 shadow-sm'"
                                            class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold text-xs sm:text-sm transition-all flex items-center gap-1.5 shrink-0">
                                            <span>ส่ง</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </template>
                        </div>

                    @else
                        {{-- Empty State: No active room selected --}}
                        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50/50">
                            <div class="w-20 h-20 rounded-3xl bg-indigo-100/80 text-indigo-600 flex items-center justify-center text-4xl mb-4 shadow-sm">
                                💬
                            </div>
                            <h3 class="font-bold text-lg text-slate-800">เลือกห้องแชตเพื่อเริ่มสนทนา</h3>
                            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mt-1.5 leading-relaxed">
                                เลือกห้องแชตจากรายการทางด้านซ้าย หรือเปิดจากหน้ารายการคำขอแลกเปลี่ยนเพื่อประสานงานส่งมอบหนังสือ
                            </p>
                            <a href="{{ route('exchange-requests.index') }}" class="mt-5 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 shadow-sm transition-colors">
                                🔄 ไปที่รายการคำขอแลกเปลี่ยน
                            </a>
                        </div>
                    @endif

                </div>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- MODALS: MEETUP, DELIVERY, REPORT, LIGHTBOX --}}
        {{-- ========================================================= --}}

        {{-- 1. MEETUP MODAL --}}
        <div x-show="showMeetupModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="showMeetupModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-base text-slate-800 flex items-center gap-2">
                        <span>📅</span> <span>เสนอนัดรับหนังสือด้วยตนเอง</span>
                    </h3>
                    <button type="button" @click="showMeetupModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form @submit.prevent="submitMeetup()">
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">สถานที่นัดรับ <span class="text-red-500">*</span></label>
                            <input type="text" x-model="meetupForm.location" required placeholder="เช่น BTS สยาม ประตู 3, หน้าหอสมุด ม.เกษตร" class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">วันที่ <span class="text-red-500">*</span></label>
                                <input type="date" x-model="meetupForm.date" required class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">เวลา <span class="text-red-500">*</span></label>
                                <input type="time" x-model="meetupForm.time" required class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">หมายเหตุเพิ่มเติม</label>
                            <textarea x-model="meetupForm.notes" rows="2" placeholder="เช่น ใส่เสื้อสีน้ำเงิน, สะดวกช่วงบ่ายโมงเป็นต้นไป" class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" @click="showMeetupModal = false" class="px-3.5 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 text-xs font-semibold">ยกเลิก</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">ส่งใบนัดรับ</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. DELIVERY MODAL --}}
        <div x-show="showDeliveryModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="showDeliveryModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-base text-slate-800 flex items-center gap-2">
                        <span>📦</span> <span>แจ้งรายละเอียดจัดส่งพัสดุ</span>
                    </h3>
                    <button type="button" @click="showDeliveryModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form @submit.prevent="submitDelivery()">
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">บริษัทขนส่ง <span class="text-red-500">*</span></label>
                            <select x-model="deliveryForm.carrier" required class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500">
                                <option value="">-- เลือกบริษัทขนส่ง --</option>
                                <option value="ไปรษณีย์ไทย (EMS)">ไปรษณีย์ไทย (EMS)</option>
                                <option value="Flash Express">Flash Express</option>
                                <option value="Kerry Express / KEX">Kerry Express / KEX</option>
                                <option value="J&T Express">J&T Express</option>
                                <option value="Shopee Xpress / SPX">Shopee Xpress / SPX</option>
                                <option value="อื่นๆ">อื่นๆ</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">เลขพัสดุ (Tracking Number) <span class="text-red-500">*</span></label>
                            <input type="text" x-model="deliveryForm.tracking_number" required placeholder="เช่น TH0123456789 หรือ EF123456789TH" class="w-full rounded-xl border-slate-200 text-xs font-mono focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">หมายเหตุเพิ่มเติม</label>
                            <textarea x-model="deliveryForm.notes" rows="2" placeholder="เช่น ห่อกันกระแทก 3 ชั้น จัดส่งให้ช่วงเช้าแล้วครับ" class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" @click="showDeliveryModal = false" class="px-3.5 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 text-xs font-semibold">ยกเลิก</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs">ส่งเลขพัสดุ</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. REPORT MODAL --}}
        <div x-show="showReportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="showReportModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-base text-slate-800 flex items-center gap-2">
                        <span>⚠️</span> <span>รายงานต่อผู้ดูแลระบบ</span>
                    </h3>
                    <button type="button" @click="showReportModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form method="POST" action="{{ route('reports.store') }}">
                    @csrf
                    <input type="hidden" name="reported_user_id" :value="reportTarget.userId || '{{ $counterpart->id ?? '' }}'">
                    <input type="hidden" name="chat_room_id" value="{{ $activeRoom->id ?? '' }}">
                    <input type="hidden" name="chat_message_id" :value="reportTarget.type === 'message' ? reportTarget.id : ''">

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">เหตุผลที่ต้องการรายงาน <span class="text-red-500">*</span></label>
                            <select name="reason" required class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500">
                                <option value="">-- เลือกเหตุผล --</option>
                                <option value="ข้อความไม่สุภาพ / คุกคาม / หยาบคาย">ข้อความไม่สุภาพ / คุกคาม / หยาบคาย</option>
                                <option value="พฤติกรรมหลอกลวง / ฉ้อโกง">พฤติกรรมหลอกลวง / ฉ้อโกง</option>
                                <option value="นัดหมายแล้วไม่มาส่ง / เบี้ยวการแลกเปลี่ยน">นัดหมายแล้วไม่มาส่ง / เบี้ยวการแลกเปลี่ยน</option>
                                <option value="สินค้าไม่ตรงกับรูปภาพหรือรายละเอียด">สินค้าไม่ตรงกับรูปภาพหรือรายละเอียด</option>
                                <option value="สแปมหรือโฆษณา">สแปมหรือโฆษณา</option>
                                <option value="อื่นๆ">อื่นๆ</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">รายละเอียดเพิ่มเติม</label>
                            <textarea name="details" rows="3" placeholder="ระบุข้อมูลเพิ่มเติมเพื่อให้ผู้ดูแลระบบตรวจสอบได้แม่นยำยิ่งขึ้น" class="w-full rounded-xl border-slate-200 text-xs focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" @click="showReportModal = false" class="px-3.5 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 text-xs font-semibold">ยกเลิก</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-xs">ส่งรายงาน</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. IMAGE LIGHTBOX MODAL --}}
        <div x-show="lightboxImage" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4" @click="lightboxImage = null">
            <div class="relative max-w-4xl max-h-[90vh]">
                <img :src="lightboxImage" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl">
                <button type="button" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-white text-slate-800 font-bold flex items-center justify-center shadow-lg">✕</button>
            </div>
        </div>

    </div>

    {{-- Chat Application Alpine Component Script --}}
    <script>
        function chatApp(config) {
            return {
                activeRoomId: config.activeRoomId,
                currentUserId: config.currentUserId,
                messages: config.initialMessages || [],
                fetchUrl: config.fetchUrl,
                sendUrl: config.sendUrl,
                structuredUrl: config.structuredUrl,
                typingUrl: config.typingUrl,
                isBlocked: config.isBlocked,
                counterpartOnline: config.counterpartOnline,
                counterpartLastSeen: 'ออฟไลน์',
                counterpartName: 'สมาชิก',
                csrfToken: config.csrfToken,
                
                // UI state
                mobileShowChat: true,
                searchQuery: '',
                textInput: '',
                imageFile: null,
                imagePreview: null,
                isTyping: false,
                typingTimeout: null,
                pollingInterval: null,
                
                // Modals
                showMeetupModal: false,
                showDeliveryModal: false,
                showReportModal: false,
                reportTarget: {},
                lightboxImage: null,

                meetupForm: {
                    location: '',
                    date: '',
                    time: '',
                    notes: ''
                },

                deliveryForm: {
                    carrier: '',
                    tracking_number: '',
                    notes: ''
                },

                initChat() {
                    this.scrollToBottom();

                    // Start real-time polling if active room exists
                    if (this.activeRoomId && this.fetchUrl) {
                        this.pollingInterval = setInterval(() => {
                            this.fetchNewMessages();
                        }, 2500);
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('messagesContainer');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                matchesSearch(name, book1, book2) {
                    if (!this.searchQuery.trim()) return true;
                    const q = this.searchQuery.toLowerCase();
                    return name.toLowerCase().includes(q) || book1.toLowerCase().includes(q) || book2.toLowerCase().includes(q);
                },

                handleImageSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imageFile = file;
                        this.imagePreview = URL.createObjectURL(file);
                    }
                },

                clearImage() {
                    this.imageFile = null;
                    this.imagePreview = null;
                },

                openLightbox(url) {
                    this.lightboxImage = url;
                },

                handleKeyup() {
                    // Send typing heartbeat (debounced)
                    if (this.activeRoomId && this.typingUrl) {
                        if (!this.typingTimeout) {
                            fetch(this.typingUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': this.csrfToken,
                                    'Content-Type': 'application/json'
                                }
                            });
                            this.typingTimeout = setTimeout(() => {
                                this.typingTimeout = null;
                            }, 3000);
                        }
                    }
                },

                playNotificationSound() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); // A5
                        gain.gain.setValueAtTime(0.15, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.25);
                    } catch(e) {}
                },

                async fetchNewMessages() {
                    if (!this.fetchUrl) return;

                    const lastMsg = this.messages.length > 0 ? this.messages[this.messages.length - 1] : null;
                    const lastId = lastMsg ? lastMsg.id : 0;

                    try {
                        const res = await fetch(`${this.fetchUrl}?after_id=${lastId}`);
                        if (!res.ok) return;

                        const data = await res.json();
                        
                        this.isTyping = data.is_typing;
                        this.counterpartName = data.counterpart_name;
                        this.counterpartOnline = data.counterpart_online;
                        this.counterpartLastSeen = data.counterpart_last_seen;

                        if (data.messages && data.messages.length > 0) {
                            let hasNewFromOther = false;

                            data.messages.forEach(msg => {
                                // Check if not duplicate
                                if (!this.messages.some(m => m.id === msg.id)) {
                                    this.messages.push(msg);
                                    if (!msg.is_me) hasNewFromOther = true;
                                }
                            });

                            if (hasNewFromOther) {
                                this.playNotificationSound();
                            }

                            this.scrollToBottom();
                        }
                    } catch (err) {
                        console.error('Fetch messages error:', err);
                    }
                },

                async sendTextMessage() {
                    const text = this.textInput.trim();
                    if (!text && !this.imageFile) return;

                    const formData = new FormData();
                    if (text) formData.append('message', text);
                    if (this.imageFile) formData.append('image', this.imageFile);

                    this.textInput = '';
                    const fileSent = this.imageFile;
                    this.clearImage();

                    try {
                        const res = await fetch(this.sendUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (res.ok) {
                            const data = await res.json();
                            if (data.success && data.message) {
                                this.messages.push(data.message);
                                this.scrollToBottom();
                            }
                        }
                    } catch (err) {
                        console.error('Send message error:', err);
                    }
                },

                async submitMeetup() {
                    try {
                        const res = await fetch(this.structuredUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                card_type: 'meetup',
                                meetup_location: this.meetupForm.location,
                                meetup_date: this.meetupForm.date,
                                meetup_time: this.meetupForm.time,
                                meetup_notes: this.meetupForm.notes
                            })
                        });

                        if (res.ok) {
                            this.showMeetupModal = false;
                            this.meetupForm = { location: '', date: '', time: '', notes: '' };
                            this.fetchNewMessages();
                        }
                    } catch(err) {
                        console.error('Submit meetup error:', err);
                    }
                },

                async submitDelivery() {
                    try {
                        const res = await fetch(this.structuredUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                card_type: 'delivery',
                                carrier: this.deliveryForm.carrier,
                                tracking_number: this.deliveryForm.tracking_number,
                                delivery_notes: this.deliveryForm.notes
                            })
                        });

                        if (res.ok) {
                            this.showDeliveryModal = false;
                            this.deliveryForm = { carrier: '', tracking_number: '', notes: '' };
                            this.fetchNewMessages();
                        }
                    } catch(err) {
                        console.error('Submit delivery error:', err);
                    }
                },

                async deleteMsg(msgId) {
                    if (!confirm('ต้องการยกเลิกข้อความนี้ใช่หรือไม่?')) return;

                    try {
                        const res = await fetch(`/chats/messages/${msgId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            const found = this.messages.find(m => m.id === msgId);
                            if (found) {
                                found.deleted_by_sender = true;
                                found.message = 'ข้อความนี้ถูกยกเลิกแล้ว';
                                found.image_url = null;
                            }
                        }
                    } catch(err) {
                        console.error('Delete message error:', err);
                    }
                }
            };
        }
    </script>
</x-app-layout>
