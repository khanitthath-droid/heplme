<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>🔎</span>
                    <span>จัดการหนังสือที่ต้องการ (Wanted Books Management)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    ตรวจสอบรายการหนังสือที่สมาชิกลงประกาศตามหาในระบบ
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
                        <h3 class="font-bold text-lg text-slate-800">รายการหนังสือที่สมาชิกกำลังตามหา</h3>
                        <p class="text-xs text-slate-500 mt-0.5">รวมประกาศค้นหาทั้งหมดในระบบ</p>
                    </div>

                    <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-xs border border-amber-200">
                        {{ $wantedBooks->count() }} รายการ
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">ID</th>
                                <th class="py-3.5 px-6">รูป</th>
                                <th class="py-3.5 px-6">หนังสือที่ต้องการ</th>
                                <th class="py-3.5 px-6">ผู้ลงประกาศ</th>
                                <th class="py-3.5 px-6">รายละเอียด</th>
                                <th class="py-3.5 px-6 text-center">จัดการ</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($wantedBooks as $wantedBook)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6 text-xs font-mono text-slate-400">
                                        #{{ $wantedBook->id }}
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="w-12 h-16 bg-slate-100 rounded-lg overflow-hidden shrink-0 shadow-2xs">
                                            @if($wantedBook->image)
                                                <img src="{{ asset('storage/' . $wantedBook->image) }}" 
                                                     alt="{{ $wantedBook->title }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-lg bg-amber-50 text-amber-500">
                                                    🔎
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-800 line-clamp-1" title="{{ $wantedBook->title }}">
                                            {{ $wantedBook->title }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            {{ $wantedBook->author ? 'ผู้แต่ง: ' . $wantedBook->author : 'ไม่ระบุผู้แต่ง' }}
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($wantedBook->user)
                                            <div class="font-semibold text-slate-800 text-xs sm:text-sm">
                                                {{ $wantedBook->user->name }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                {{ $wantedBook->user->email }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs">ไม่พบข้อมูลสมาชิก</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($wantedBook->description)
                                            <p class="text-xs text-slate-600 line-clamp-2 max-w-xs">
                                                {{ $wantedBook->description }}
                                            </p>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.wanted-books.destroy', $wantedBook) }}" method="POST"
                                              onsubmit="return confirm('ยืนยันต้องการลบรายการที่ตามหา {{ $wantedBook->title }} ออกจากระบบ?');">
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
                                    <td colspan="6" class="py-12 px-6 text-center text-slate-400">
                                        ยังไม่มีรายการหนังสือที่ต้องการในระบบ
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