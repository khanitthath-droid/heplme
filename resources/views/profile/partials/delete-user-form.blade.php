<section class="space-y-4">
    <header class="pb-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-red-600 flex items-center gap-2">
            <span>⚠️</span>
            <span>ลบบัญชีผู้ใช้งาน (Delete Account)</span>
        </h2>

        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            เมื่อลบบัญชี ข้อมูลหนังสือ รายการที่ตามหา และประวัติคำขอทั้งหมดจะถูกลบอย่างถาวร
        </p>
    </header>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-5 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs sm:text-sm border border-red-200 transition-all"
        >
            🗑️ ลบบัญชีผู้ใช้นี้
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')

            <div class="text-center sm:text-left">
                <h2 class="text-lg font-bold text-slate-900">
                    คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีผู้ใช้นี้?
                </h2>

                <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                    การดำเนินการนี้ไม่สามารถย้อนกลับได้ ข้อมูลหนังสือ คำขอแลกเปลี่ยน และประวัติทั้งหมดของคุณจะถูกลบออกจากระบบทันที กรุณากรอกรหัสผ่านเพื่อยืนยัน
                </p>
            </div>

            <div class="mt-5">
                <label for="password" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                    รหัสผ่านเพื่อยืนยัน
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 text-sm transition-all"
                    placeholder="กรอกรหัสผ่านของคุณ"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button 
                    type="button" 
                    x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-sm transition-colors"
                >
                    ยกเลิก
                </button>

                <button 
                    type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-sm transition-all"
                >
                    ยืนยันลบบัญชี
                </button>
            </div>
        </form>
    </x-modal>
</section>
