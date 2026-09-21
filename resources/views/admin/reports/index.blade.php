<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>⚠️</span>
                    <span>ศูนย์จัดการการรายงานปัญหา (Admin Moderation)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    ตรวจสอบและจัดการข้อร้องเรียน สแปม หรือพฤติกรรมไม่เหมาะสมในระบบแชตและคำขอแลกเปลี่ยน
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs sm:text-sm font-semibold transition-all self-start sm:self-auto">
                <span>←</span>
                <span>กลับสู่แผงควบคุม</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-lg p-1">✕</button>
                </div>
            @endif

            {{-- Filter Tabs --}}
            <div class="bg-white p-1.5 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center gap-2 max-w-xl">
                <a href="{{ route('admin.reports.index', ['status' => 'all']) }}" 
                   class="flex-1 py-2 px-3 rounded-xl text-xs sm:text-sm font-semibold text-center transition-colors {{ $status === 'all' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    ทั้งหมด ({{ $reports->total() }})
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" 
                   class="flex-1 py-2 px-3 rounded-xl text-xs sm:text-sm font-semibold text-center transition-colors {{ $status === 'pending' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    ⏳ รอดำเนินการ ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" 
                   class="flex-1 py-2 px-3 rounded-xl text-xs sm:text-sm font-semibold text-center transition-colors {{ $status === 'resolved' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    ✅ จัดการแล้ว ({{ $resolvedCount }})
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'dismissed']) }}" 
                   class="flex-1 py-2 px-3 rounded-xl text-xs sm:text-sm font-semibold text-center transition-colors {{ $status === 'dismissed' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    ยกเลิก ({{ $dismissedCount }})
                </a>
            </div>

            {{-- Reports List --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="divide-y divide-slate-100">
                    @forelse($reports as $report)
                        <div class="p-6 space-y-4 hover:bg-slate-50/50 transition-colors">
                            
                            {{-- Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-lg {{ $report->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($report->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $report->status === 'pending' ? '⏳' : ($report->status === 'resolved' ? '✅' : '⚪') }}
                                    </span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-sm text-slate-800">
                                                รายงาน #{{ $report->id }}: {{ $report->reason }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide {{ $report->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($report->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                                {{ $report->status }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">
                                            รายงานเมื่อ {{ $report->created_at->locale('th')->translatedFormat('j F Y เวลา H:i น.') }} ({{ $report->created_at->locale('th')->diffForHumans() }})
                                        </p>
                                    </div>
                                </div>

                                {{-- Status Update Form --}}
                                <form method="POST" action="{{ route('admin.reports.update', $report->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-xl border-slate-200 text-xs py-1.5 focus:ring-purple-500">
                                        <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>⏳ รอดำเนินการ</option>
                                        <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>✅ จัดการแล้ว</option>
                                        <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>ยกเลิก/ไม่พบความผิด</option>
                                    </select>
                                    <button type="submit" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors">
                                        บันทึก
                                    </button>
                                </form>
                            </div>

                            {{-- Parties Info --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/80 p-4 rounded-2xl border border-slate-100 text-xs">
                                {{-- Reporter --}}
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0">
                                        {{ mb_substr($report->reporter->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-500 text-[11px]">ผู้รายงาน:</p>
                                        <p class="font-bold text-slate-800">{{ $report->reporter->name ?? 'ไม่พบข้อมูล' }} ({{ $report->reporter->email ?? '-' }})</p>
                                    </div>
                                </div>

                                {{-- Reported User --}}
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center shrink-0">
                                        {{ mb_substr($report->reportedUser->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-500 text-[11px]">ผู้ถูกรายงาน:</p>
                                        <p class="font-bold text-red-600">{{ $report->reportedUser->name ?? 'ไม่พบข้อมูล' }} ({{ $report->reportedUser->email ?? '-' }})</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Details --}}
                            @if($report->details)
                                <div class="p-3 bg-amber-50/70 border border-amber-100 rounded-xl text-xs text-amber-900">
                                    <p class="font-bold mb-0.5">รายละเอียดเพิ่มเติมจากผู้รายงาน:</p>
                                    <p class="whitespace-pre-wrap leading-relaxed">{{ $report->details }}</p>
                                </div>
                            @endif

                            {{-- Reported Message Context --}}
                            @if($report->chatMessage)
                                <div class="p-4 bg-slate-100 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                            <span>💬</span> <span>ข้อความที่ถูกรายงาน:</span>
                                        </span>
                                        @if(!$report->chatMessage->deleted_by_sender)
                                            <form method="POST" action="{{ route('admin.reports.deleteMessage', $report->chatMessage->id) }}" onsubmit="return confirm('ยืนยันลบข้อความนี้ออกจากแชต?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs">
                                                    🗑️ ลบข้อความนี้ทันที
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 italic">ข้อความนี้ถูกลบ/ระงับแล้ว</span>
                                        @endif
                                    </div>
                                    <div class="p-3 bg-white rounded-xl border border-slate-200">
                                        @if($report->chatMessage->image_path)
                                            <img src="{{ asset('storage/' . $report->chatMessage->image_path) }}" class="max-h-40 rounded-lg mb-2">
                                        @endif
                                        <p class="text-slate-800">{{ $report->chatMessage->message ?? '(ไม่มีข้อความตัวอักษร)' }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Chat Room Shortcut --}}
                            @if($report->chat_room_id)
                                <div class="flex items-center gap-2 pt-1">
                                    <a href="{{ route('chats.show', $report->chat_room_id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                        <span>👁️ ตรวจสอบห้องแชตต้นเรื่อง</span> <span>→</span>
                                    </a>
                                </div>
                            @endif

                        </div>
                    @empty
                        <div class="p-12 text-center text-slate-400 text-sm">
                            <span class="text-4xl block mb-2">🎉</span>
                            ไม่มีรายการรายงานปัญหาในสถานะนี้
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($reports->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
