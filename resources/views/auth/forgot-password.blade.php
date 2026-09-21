<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
            ลืมรหัสผ่าน?
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
            กรุณากรอกอีเมลที่คุณใช้สมัครสมาชิก ระบบจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ไปให้คุณทางอีเมล
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                อีเมล (Email)
            </label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                placeholder="you@example.com"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white font-bold text-sm shadow-md hover:shadow-lg shadow-indigo-500/25 transition-all">
                📧 ส่งลิงก์รีเซ็ตรหัสผ่าน
            </button>
        </div>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            จำรหัสผ่านได้แล้ว? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800 ml-1 underline">
                กลับไปหน้าเข้าสู่ระบบ
            </a>
        </div>
    </form>
</x-guest-layout>
