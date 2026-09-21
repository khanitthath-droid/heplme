<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>🔄</span>
                    <span>จัดการคำขอแลกเปลี่ยนทั้งหมด (Exchange Requests Management)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    ตรวจสอบประวัติคำขอแลกเปลี่ยนระหว่างสมาชิกทุกคนในระบบ และสถานะความคืบหน้า
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                ← แผงควบคุม Admin
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

            {{-- Table Container --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                
                {{-- Table Top Header --}}
                <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">คำขอแลกเปลี่ยนทั้งหมดในระบบ</h3>
                        <p class="text-xs text-slate-500 mt-0.5">รวมธุรกรรมการขอแลกหนังสือทั้งหมด</p>
                    </div>

                    <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-700 font-bold text-xs border border-purple-200">
                        {{ $exchangeRequests->count() }} คำขอ
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">ID</th>
                                <th class="py-3.5 px-6">ผู้ส่งคำขอ</th>
                                <th class="py-3.5 px-6">ผู้รับคำขอ</th>
                                <th class="py-3.5 px-6">หนังสือที่เสนอ</th>
                                <th class="py-3.5 px-6">หนังสือที่ขอแลก</th>
                                <th class="py-3.5 px-6 text-center">สถานะ</th>
                                <th class="py-3.5 px-6 text-center">จัดการ</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($exchangeRequests as $request)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6 text-xs font-mono text-slate-400">
                                        #{{ $request->id }}
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($request->requester)
                                            <div class="font-semibold text-slate-800 text-xs sm:text-sm">
                                                {{ $request->requester->name }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                {{ $request->requester->email }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($request->receiver)
                                            <div class="font-semibold text-slate-800 text-xs sm:text-sm">
                                                {{ $request->receiver->name }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                {{ $request->receiver->email }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($request->offeredBook)
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-9 h-12 bg-slate-100 rounded overflow-hidden shrink-0">
                                                    @if($request->offeredBook->image)
                                                        <img src="{{ asset('storage/' . $request->offeredBook->image) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-xs bg-indigo-50 text-indigo-400">📖</div>
                                                    @endif
                                                </div>
                                                <div class="font-semibold text-xs text-slate-800 line-clamp-1 max-w-[140px]" title="{{ $request->offeredBook->title }}">
                                                    {{ $request->offeredBook->title }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs">หนังสือถูกลบ</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($request->requestedBook)
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-9 h-12 bg-slate-100 rounded overflow-hidden shrink-0">
                                                    @if($request->requestedBook->image)
                                                        <img src="{{ asset('storage/' . $request->requestedBook->image) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-xs bg-purple-50 text-purple-400">📖</div>
                                                    @endif
                                                </div>
                                                <div class="font-semibold text-xs text-slate-800 line-clamp-1 max-w-[140px]" title="{{ $request->requestedBook->title }}">
                                                    {{ $request->requestedBook->title }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs">หนังสือถูกลบ</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 text-center">
                                        @if($request->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                รอดำเนินการ
                                            </span>
                                        @elseif($request->status === 'accepted')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                ยอมรับแล้ว
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                ปฏิเสธแล้ว
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.exchange-requests.destroy', $request) }}" method="POST"
                                              onsubmit="return confirm('ยืนยันต้องการลบประวัติคำขอนี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 active:scale-95 transition-all">
                                                <span>🗑️</span> <span>ลบ</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 px-6 text-center text-slate-400">
                                        ยังไม่มีประวัติคำขอแลกเปลี่ยนในระบบ
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>