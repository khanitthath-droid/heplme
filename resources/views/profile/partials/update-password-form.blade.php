<section>
    <header class="pb-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <span>🔐</span>
            <span>เปลี่ยนรหัสผ่าน (Update Password)</span>
        </h2>

        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            โปรดตั้งรหัสผ่านที่มีความยาวอย่างน้อย 8 ตัวอักษรเพื่อความปลอดภัยของบัญชี
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                รหัสผ่านปัจจุบัน
            </label>
            <input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all" 
                autocomplete="current-password" 
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <label for="update_password_password" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                รหัสผ่านใหม่
            </label>
            <input 
                id="update_password_password" 
                name="password" 
                type="password" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all" 
                autocomplete="new-password" 
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                ยืนยันรหัสผ่านใหม่
            </label>
            <input 
                id="update_password_password_confirmation" 
                name="password_confirmation" 
                type="password" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all" 
                autocomplete="new-password" 
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center gap-4">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow transition-all">
                💾 เปลี่ยนรหัสผ่าน
            </button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1"
                >
                    <span>✅</span> เปลี่ยนรหัสผ่านสำเร็จ
                </span>
            @endif
        </div>
    </form>
</section>
