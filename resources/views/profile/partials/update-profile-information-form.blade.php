<section x-data="{ avatarPreview: null }">
    <header class="pb-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <span>👤</span>
            <span>ข้อมูลโปรไฟล์ & รูปประจำตัว</span>
        </h2>

        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            อัปเดตข้อมูลบัญชีผู้ใช้งาน รูปโปรไฟล์ และที่อยู่อีเมลของคุณ
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Avatar Section --}}
        <div>
            <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-2">
                รูปโปรไฟล์ (Profile Avatar)
            </label>

            <div class="flex items-center gap-5">
                {{-- Avatar Preview Container --}}
                <div class="relative group shrink-0">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" alt="Avatar Preview" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100 shadow-md">
                    </template>
                    <template x-if="!avatarPreview">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100 shadow-md">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br {{ $user->role === 'admin' ? 'from-purple-600 to-indigo-600' : 'from-indigo-500 to-blue-500' }} text-white font-extrabold text-2xl flex items-center justify-center ring-4 ring-indigo-100 shadow-md">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </template>
                </div>

                {{-- File Upload Input --}}
                <div class="flex-1">
                    <input 
                        type="file" 
                        name="avatar" 
                        id="avatar" 
                        accept="image/jpeg,image/png,image/webp"
                        @change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => avatarPreview = e.target.result;
                                reader.readAsDataURL(file);
                            }
                        "
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-1.5"
                    />
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        รองรับไฟล์รูปภาพ JPG, PNG, WEBP ขนาดไม่เกิน 2MB
                    </p>
                    <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
                </div>
            </div>
        </div>

        {{-- Name --}}
        <div>
            <label for="name" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                ชื่อ - นามสกุล <span class="text-red-500">*</span>
            </label>
            <input 
                id="name" 
                name="name" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all" 
                value="{{ old('name', $user->name) }}" 
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                อีเมล (Email) <span class="text-red-500">*</span>
            </label>
            <input 
                id="email" 
                name="email" 
                type="email" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all" 
                value="{{ old('email', $user->email) }}" 
                required 
                autocomplete="username" 
            />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-amber-50 rounded-xl border border-amber-200">
                    <p class="text-xs text-amber-800">
                        อีเมลของคุณยังไม่ได้รับการยืนยัน
                        <button form="send-verification" class="underline text-xs text-amber-900 font-bold hover:text-amber-700 ml-1">
                            คลิกที่นี่เพื่อส่งอีเมลยืนยันอีกครั้ง
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-xs text-emerald-600">
                            ลิงก์ยืนยันตัวตนใหม่ถูกส่งไปยังอีเมลของคุณเรียบร้อยแล้ว
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Role Indicator (Read only) --}}
        <div>
            <label class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                สิทธิ์การใช้งาน (Role)
            </label>
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                {{ $user->role === 'admin' ? '👑 ผู้ดูแลระบบ (Admin)' : '👤 สมาชิกทั่วไป (Member)' }}
            </div>
        </div>

                {{-- Exchange Contact Information --}}
        <div class="pt-5 border-t border-slate-100">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <span>🔐</span>
                    <span>ข้อมูลสำหรับการแลกเปลี่ยน</span>
                </h3>

                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    กรอกเฉพาะช่องทางที่ต้องการใช้ติดต่อกับคู่แลกเปลี่ยน
                </p>
            </div>

            {{-- Security Notice --}}
            <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="flex items-start gap-2">
                    <span class="text-base">🛡️</span>
                    <div>
                        <p class="text-xs font-bold text-amber-800">
                            ข้อมูลส่วนตัวของคุณได้รับการปกป้อง
                        </p>
                        <p class="mt-1 text-[11px] leading-5 text-amber-700">
                            ข้อมูลติดต่อจะไม่แสดงต่อผู้ใช้อื่น
                            จนกว่าคำขอแลกเปลี่ยนจะได้รับการยอมรับ
                            และไม่ควรกรอกที่อยู่บ้านหรือข้อมูลสำคัญอื่น ๆ
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Line ID --}}
                <div>
                    <label for="line_id" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                        Line ID
                    </label>

                    <input
                        id="line_id"
                        name="line_id"
                        type="text"
                        maxlength="50"
                        value="{{ old('line_id', $user->line_id) }}"
                        placeholder="เช่น book123"
                        autocomplete="off"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                    />

                    <p class="text-[11px] text-slate-400 mt-1.5">
                        กรอกเฉพาะ Line ID ที่ต้องการใช้ติดต่อ
                    </p>

                    <x-input-error class="mt-1" :messages="$errors->get('line_id')" />
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                        เบอร์โทรศัพท์
                    </label>

                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        maxlength="20"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="เช่น 08xxxxxxxx"
                        autocomplete="tel"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                    />

                    <p class="text-[11px] text-slate-400 mt-1.5">
                        ไม่จำเป็นต้องกรอก หากเลือกใช้ Line เป็นช่องทางติดต่อ
                    </p>

                    <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                </div>

                {{-- Exchange Area --}}
                <div class="sm:col-span-2">
                    <label for="exchange_area" class="block font-semibold text-xs text-slate-700 uppercase tracking-wider mb-1.5">
                        จังหวัด / พื้นที่สะดวกแลกเปลี่ยน
                    </label>

                    <input
                        id="exchange_area"
                        name="exchange_area"
                        type="text"
                        maxlength="100"
                        value="{{ old('exchange_area', $user->exchange_area) }}"
                        placeholder="เช่น นครราชสีมา"
                        autocomplete="off"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all"
                    />

                    <p class="text-[11px] text-slate-400 mt-1.5">
                        ระบุเพียงจังหวัดหรือพื้นที่กว้าง ๆ ห้ามระบุบ้านเลขที่หรือที่อยู่ส่วนตัว
                    </p>

                    <x-input-error class="mt-1" :messages="$errors->get('exchange_area')" />
                </div>

            </div>
        </div>

        {{-- Submit Button --}}
        <div class="pt-4 border-t border-slate-100 flex items-center gap-4">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-sm shadow-sm hover:shadow transition-all">
                💾 บันทึกการเปลี่ยนแปลง
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1"
                >
                    <span>✅</span> บันทึกข้อมูลสำเร็จ
                </span>
            @endif
        </div>
    </form>
</section>
