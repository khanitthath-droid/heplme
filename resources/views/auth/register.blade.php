<x-guest-layout>
    
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
            สมัครสมาชิกใหม่
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            ร่วมเป็นส่วนหนึ่งของชุมชนแบ่งปันหนังสือ BookCycle
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                ชื่อ - นามสกุล
            </label>
            <input 
                id="name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                autocomplete="name" 
                placeholder="เช่น สมชาย รักการอ่าน"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

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
                autocomplete="username" 
                placeholder="you@example.com"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                รหัสผ่าน (Password)
            </label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password" 
                placeholder="ความยาวอย่างน้อย 8 ตัวอักษร"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                ยืนยันรหัสผ่าน (Confirm Password)
            </label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password" 
                placeholder="กรอกรหัสผ่านอีกครั้ง"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white font-bold text-sm shadow-md hover:shadow-lg shadow-indigo-500/25 transition-all">
                📝 สมัครสมาชิก
            </button>
        </div>

        <!-- Login Link -->
        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            มีบัญชีผู้ใช้งานอยู่แล้ว? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800 ml-1 underline">
                เข้าสู่ระบบที่นี่
            </a>
        </div>
    </form>

</x-guest-layout>
