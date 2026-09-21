<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>👥</span>
                    <span>จัดการผู้ใช้งาน (Users Management)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    ตรวจสอบรายชื่อสมาชิกทั้งหมด สิทธิ์การใช้งาน และจัดการบัญชีในระบบ
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

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <p class="font-bold text-sm">ไม่สำเร็จ!</p>
                            <p class="text-xs text-red-700 mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700 text-lg p-1">✕</button>
                </div>
            @endif

            {{-- Table Container --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                
                {{-- Table Top Header --}}
                <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">รายชื่อสมาชิกทั้งหมด</h3>
                        <p class="text-xs text-slate-500 mt-0.5">รวมผู้ใช้งานทั้งหมดในฐานข้อมูล</p>
                    </div>

                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200">
                        {{ $users->count() }} บัญชี
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">ID</th>
                                <th class="py-3.5 px-6">ผู้ใช้งาน</th>
                                <th class="py-3.5 px-6">อีเมล</th>
                                <th class="py-3.5 px-6">สิทธิ์ (Role)</th>
                                <th class="py-3.5 px-6">วันที่สมัคร</th>
                                <th class="py-3.5 px-6 text-center">จัดการ</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-4 px-6 text-xs font-mono text-slate-400">
                                        #{{ $user->id }}
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover shadow-xs ring-1 ring-slate-200 shrink-0">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $user->role === 'admin' ? 'from-purple-600 to-indigo-600' : 'from-blue-500 to-indigo-500' }} text-white font-bold text-xs flex items-center justify-center shadow-xs shrink-0">
                                                    {{ mb_substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $user->name }}</div>
                                                @if($user->id === auth()->id())
                                                    <span class="text-[10px] text-indigo-600 font-semibold">(คุณ)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 text-slate-600 text-xs sm:text-sm">
                                        {{ $user->email }}
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
                                                👑 Admin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-200">
                                                👤 User
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 text-xs text-slate-400">
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                                    </td>

                                    <td class="py-4 px-6 text-center">
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('คำเตือน: คุณต้องการลบผู้ใช้งาน {{ $user->name }} ออกจากระบบใช่หรือไม่?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 active:scale-95 transition-all">
                                                    <span>🗑️</span> <span>ลบ</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium italic">
                                                บัญชีปัจจุบัน
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 px-6 text-center text-slate-400">
                                        ยังไม่มีผู้ใช้งานในระบบ
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