<x-guest-layout>
    
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
            เข้าสู่ระบบ
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            ยินดีต้อนรับกลับ! เข้าสู่ระบบเพื่อเริ่มแลกเปลี่ยนหนังสือ
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
                autocomplete="username" 
                placeholder="you@example.com"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider">
                    รหัสผ่าน (Password)
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-indigo-600 hover:text-indigo-800 font-medium" href="{{ route('password.request') }}">
                        ลืมรหัสผ่าน?
                    </a>
                @endif
            </div>

            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password" 
                placeholder="••••••••"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-lg border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                <span class="text-xs text-slate-600 font-medium">จดจำการเข้าสู่ระบบ</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white font-bold text-sm shadow-md hover:shadow-lg shadow-indigo-500/25 transition-all">
                🔐 เข้าสู่ระบบ
            </button>
        </div>

        <!-- Register Link -->
        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            ยังไม่มีบัญชีผู้ใช้งาน? 
            <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 ml-1 underline">
                สมัครสมาชิกที่นี่
            </a>
        </div>
    </form>

</x-guest-layout>
