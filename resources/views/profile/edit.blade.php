<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span>👤</span>
                    <span>ตั้งค่าโปรไฟล์ & บัญชีผู้ใช้</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    จัดการรูปประจำตัว ข้อมูลส่วนตัว และความปลอดภัยของบัญชี
                </p>
            </div>

            <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors self-start sm:self-auto">
                ← กลับสู่ Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. Profile Information & Avatar --}}
            <div class="p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- 2. Update Password --}}
            <div class="p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm">
                @include('profile.partials.update-password-form')
            </div>

            {{-- 3. Delete Account --}}
            <div class="p-6 sm:p-8 bg-white rounded-3xl border border-red-100 shadow-sm">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>
