<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <div class="flex items-center gap-4 lg:gap-6">
                <!-- Logo -->
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="flex items-center gap-2.5 group shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr {{ Auth::user()->role === 'admin' ? 'from-purple-600 to-indigo-600' : 'from-indigo-600 to-indigo-400' }} flex items-center justify-center text-white shadow-md {{ Auth::user()->role === 'admin' ? 'shadow-purple-500/20' : 'shadow-indigo-500/20' }} group-hover:scale-105 transition-transform duration-200">
                        <span class="text-xl">{{ Auth::user()->role === 'admin' ? '👑' : '📚' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">
                                BookCycle
                            </span>
                            @if(Auth::user()->role === 'admin')
                                <span class="px-1.5 py-0.5 rounded-md bg-purple-100 text-purple-700 text-[10px] font-extrabold uppercase tracking-wide">
                                    ADMIN
                                </span>
                            @endif
                        </div>
                        <span class="text-[11px] font-medium text-slate-400 -mt-0.5">
                            {{ Auth::user()->role === 'admin' ? 'ระบบจัดการส่วนกลาง' : 'ระบบแลกเปลี่ยนหนังสือ' }}
                        </span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="hidden lg:flex items-center gap-1.5 xl:gap-2">
                    @if(Auth::user()->role === 'admin')
                        {{-- ==========================================
                            👑 ADMIN MENUS (5 เมนูสำหรับ Admin เท่านั้น)
                        =========================================== --}}

                        <!-- 1. ภาพรวมระบบ -->
                        @php $isDashboardActive = request()->routeIs('admin.dashboard'); @endphp
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold transition-all duration-150 {{ $isDashboardActive ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50/80' }}">
                            <span>👑</span>
                            <span>ภาพรวมระบบ</span>
                        </a>

                        <!-- 2. จัดการสมาชิก -->
                        @php $isUsersActive = request()->routeIs('admin.users.*'); @endphp
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold transition-all duration-150 {{ $isUsersActive ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50/80' }}">
                            <span>👥</span>
                            <span>จัดการสมาชิก</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $isUsersActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                                ({{ $navUsersCount ?? 0 }})
                            </span>
                        </a>

                        <!-- 3. จัดการหนังสือ -->
                        @php $isBooksActive = request()->routeIs('admin.books.*'); @endphp
                        <a href="{{ route('admin.books.index') }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold transition-all duration-150 {{ $isBooksActive ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50/80' }}">
                            <span>📚</span>
                            <span>จัดการหนังสือ</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $isBooksActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                                ({{ $navBooksCount ?? 0 }})
                            </span>
                        </a>

                        <!-- 4. หนังสือที่ต้องการ -->
                        @php $isWantedActive = request()->routeIs('admin.wanted-books.*'); @endphp
                        <a href="{{ route('admin.wanted-books.index') }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold transition-all duration-150 {{ $isWantedActive ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50/80' }}">
                            <span>🔎</span>
                            <span>หนังสือที่ต้องการ</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $isWantedActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                                ({{ $navWantedBooksCount ?? 0 }})
                            </span>
                        </a>

                        <!-- 5. คำขอแลกเปลี่ยน -->
                        @php $isExchangeActive = request()->routeIs('admin.exchange-requests.*'); @endphp
                        <a href="{{ route('admin.exchange-requests.index') }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold transition-all duration-150 {{ $isExchangeActive ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50/80' }}">
                            <span>🎯</span>
                            <span>คำขอแลกเปลี่ยน</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $isExchangeActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                                ({{ $navExchangeRequestsCount ?? 0 }})
                            </span>
                        </a>

                        <!-- 6. รายงานปัญหา -->
                        @php $isReportsActive = request()->routeIs('admin.reports.*'); @endphp
                        <a href="{{ route('admin.reports.index') }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold transition-all duration-150 {{ $isReportsActive ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50/80' }}">
                            <span>⚠️</span>
                            <span>รายงานปัญหา</span>
                            @if(!empty($navReportsCount) && $navReportsCount > 0)
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white animate-pulse">
                                    ({{ $navReportsCount }})
                                </span>
                            @endif
                        </a>

                    @else
                        {{-- ==========================================
                            👤 USER MENUS (เมนูสำหรับสมาชิกทั่วไป)
                        =========================================== --}}

                        <!-- Dashboard -->
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <span>🏠</span>
                            <span>Dashboard</span>
                        </x-nav-link>

                        <!-- Books -->
                        <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')">
                            <span>📚</span>
                            <span>หนังสือของฉัน</span>
                        </x-nav-link>

                        <!-- Wanted Books -->
                        <x-nav-link :href="route('wanted-books.index')" :active="request()->routeIs('wanted-books.*')">
                            <span>🔎</span>
                            <span>หนังสือที่ต้องการ</span>
                        </x-nav-link>

                        <!-- Matching -->
                        <x-nav-link :href="route('matching.index')" :active="request()->routeIs('matching.*')">
                            <span>🎯</span>
                            <span>หนังสือที่ตรงกัน</span>
                        </x-nav-link>

                        <!-- Exchange Requests -->
                        <x-nav-link :href="route('exchange-requests.index')" :active="request()->routeIs('exchange-requests.*')">
                            <span>🔄</span>
                            <span>คำขอแลกเปลี่ยน</span>
                        </x-nav-link>

                        <!-- Chats / Messages -->
                        <x-nav-link :href="route('chats.index')" :active="request()->routeIs('chats.*')">
                            <span>💬</span>
                            <span>แชตพูดคุย</span>
                            @if(!empty($navUnreadChatCount) && $navUnreadChatCount > 0)
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-500 text-white animate-pulse">
                                    {{ $navUnreadChatCount }}
                                </span>
                            @endif
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex items-center gap-3">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100 hover:border-slate-300 focus:outline-none transition ease-in-out duration-150">
                            <!-- Avatar circle -->
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover shadow-xs ring-1 ring-slate-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ Auth::user()->role === 'admin' ? 'from-purple-600 to-indigo-600' : 'from-indigo-500 to-blue-500' }} text-white font-bold text-xs flex items-center justify-center shadow-xs">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif

                            <div class="text-start">
                                <div class="text-xs font-bold text-slate-800 leading-tight">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-[10px] {{ Auth::user()->role === 'admin' ? 'text-purple-600 font-semibold' : 'text-slate-500' }}">
                                    {{ Auth::user()->role === 'admin' ? '👑 ผู้ดูแลระบบ' : '👤 สมาชิก' }}
                                </div>
                            </div>

                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User info header inside dropdown -->
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2.5">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover ring-1 ring-slate-200">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-blue-500 text-white font-bold text-xs flex items-center justify-center">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <!-- If Admin, show link to Switch to User View / Dashboard -->
                        @if(Auth::user()->role === 'admin')
                            <x-dropdown-link :href="route('dashboard')" class="text-indigo-600 font-semibold hover:bg-indigo-50">
                                🏠 {{ __('สลับไปหน้า Dashboard สมาชิก') }}
                            </x-dropdown-link>
                            <div class="border-t border-slate-100 my-1"></div>
                        @endif

                        <!-- Profile -->
                        <x-dropdown-link :href="route('profile.edit')">
                            👤 {{ __('แก้ไขโปรไฟล์ & รูปประจำตัว') }}
                        </x-dropdown-link>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 hover:bg-red-50">
                                🚪 {{ __('ออกจากระบบ') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:hidden border-t border-slate-200 bg-white/95 px-4 pt-3 pb-5 space-y-1 shadow-lg">
        @if(Auth::user()->role === 'admin')
            <!-- Admin Mobile Menu -->
            <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-purple-600">
                เมนูจัดการระบบ (Admin)
            </div>

            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="{{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white font-bold border-l-4 border-purple-800' : '' }}">
                <span>👑</span> <span>ภาพรวมระบบ</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="{{ request()->routeIs('admin.users.*') ? 'bg-purple-600 text-white font-bold border-l-4 border-purple-800' : '' }}">
                <span>👥</span> <span>จัดการสมาชิก ({{ $navUsersCount ?? 0 }})</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.*')" class="{{ request()->routeIs('admin.books.*') ? 'bg-purple-600 text-white font-bold border-l-4 border-purple-800' : '' }}">
                <span>📚</span> <span>จัดการหนังสือ ({{ $navBooksCount ?? 0 }})</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.wanted-books.index')" :active="request()->routeIs('admin.wanted-books.*')" class="{{ request()->routeIs('admin.wanted-books.*') ? 'bg-purple-600 text-white font-bold border-l-4 border-purple-800' : '' }}">
                <span>🔎</span> <span>หนังสือที่ต้องการ ({{ $navWantedBooksCount ?? 0 }})</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.exchange-requests.index')" :active="request()->routeIs('admin.exchange-requests.*')" class="{{ request()->routeIs('admin.exchange-requests.*') ? 'bg-purple-600 text-white font-bold border-l-4 border-purple-800' : '' }}">
                <span>🎯</span> <span>คำขอแลกเปลี่ยน ({{ $navExchangeRequestsCount ?? 0 }})</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')" class="{{ request()->routeIs('admin.reports.*') ? 'bg-purple-600 text-white font-bold border-l-4 border-purple-800' : '' }}">
                <span>⚠️</span> <span>รายงานปัญหา {{ !empty($navReportsCount) && $navReportsCount > 0 ? "({$navReportsCount})" : '' }}</span>
            </x-responsive-nav-link>

        @else
            <!-- User Mobile Menu -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <span>🏠</span> <span>Dashboard</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')">
                <span>📚</span> <span>หนังสือของฉัน</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('wanted-books.index')" :active="request()->routeIs('wanted-books.*')">
                <span>🔎</span> <span>หนังสือที่ต้องการ</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('matching.index')" :active="request()->routeIs('matching.*')">
                <span>🎯</span> <span>หนังสือที่ตรงกัน</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('exchange-requests.index')" :active="request()->routeIs('exchange-requests.*')">
                <span>🔄</span> <span>คำขอแลกเปลี่ยน</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('chats.index')" :active="request()->routeIs('chats.*')">
                <span>💬</span> <span>แชตพูดคุย</span>
                @if(!empty($navUnreadChatCount) && $navUnreadChatCount > 0)
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white">
                        {{ $navUnreadChatCount }}
                    </span>
                @endif
            </x-responsive-nav-link>
        @endif

        <!-- Responsive User Profile -->
        <div class="pt-4 mt-3 border-t border-slate-200/80">
            <div class="flex items-center gap-3 px-3 py-2 bg-slate-50 rounded-xl mb-2">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover ring-1 ring-slate-200">
                @else
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ Auth::user()->role === 'admin' ? 'from-purple-600 to-indigo-600' : 'from-indigo-500 to-blue-500' }} text-white font-bold text-xs flex items-center justify-center">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="font-bold text-sm text-slate-800 leading-tight">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-1">
                @if(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('dashboard')">
                        <span>🏠</span> <span>{{ __('ดูหน้า Dashboard สมาชิก') }}</span>
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('profile.edit')">
                    <span>👤</span> <span>{{ __('แก้ไขโปรไฟล์ & รูปประจำตัว') }}</span>
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                        <span>🚪</span> <span>{{ __('ออกจากระบบ') }}</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>