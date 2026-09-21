<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BookCycle - ระบบแลกเปลี่ยนหนังสือมือสองแบบหมุนเวียน</title>

        <!-- Fonts (Prompt & Plus Jakarta Sans) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-theme-user selection:bg-indigo-500 selection:text-white">
        
        <!-- 1. Top Navigation Bar -->
        <nav class="bg-white/85 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    
                    <!-- Logo -->
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-400 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                            <span class="text-xl">📚</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">
                                BookCycle
                            </span>
                            <span class="text-[11px] font-medium text-slate-400 -mt-0.5">
                                แลกเปลี่ยนหนังสือหมุนเวียน
                            </span>
                        </div>
                    </a>

                    <!-- Nav Links -->
                    <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                        <a href="#features" class="hover:text-indigo-600 transition-colors">จุดเด่นระบบ</a>
                        <a href="#how-it-works" class="hover:text-indigo-600 transition-colors">ขั้นตอนการแลก</a>
                        <a href="#books-showcase" class="hover:text-indigo-600 transition-colors">หนังสือในระบบ</a>
                    </div>

                    <!-- Auth Actions -->
                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('dashboard') }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold shadow-sm hover:shadow transition-all">
                                    <span>🏠 เข้าสู่ Dashboard</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-indigo-600 hover:bg-slate-100 transition-all">
                                    🔐 เข้าสู่ระบบ
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs sm:text-sm font-bold shadow-md shadow-indigo-500/20 transition-all">
                                        <span>📝 สมัครสมาชิก</span>
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>

                </div>
            </div>
        </nav>

        <!-- 2. Hero Section -->
        <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-indigo-50/50 via-white to-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto space-y-6">
                    
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-100/80 text-indigo-700 text-xs sm:text-sm font-bold border border-indigo-200/80 shadow-2xs animate-bounce">
                        <span>✨</span>
                        <span>แพลตฟอร์มแลกเปลี่ยนหนังสืออันดับ 1 ของคนรักการอ่าน</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        ระบบแลกเปลี่ยนหนังสือมือสอง<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-800">
                            แบบหมุนเวียน (BookCycle)
                        </span>
                    </h1>

                    <!-- Subtitle Tagline -->
                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
                        เปลี่ยนหนังสือบนหิ้งที่อ่านจบแล้ว ให้กลายเป็นหนังสือเล่มโปรดเล่มใหม่ ส่งต่อความรู้แบบไม่มีที่สิ้นสุด ด้วยระบบจับคู่อัจฉริยะที่ง่าย สะดวก และไม่มีค่าใช้จ่าย
                    </p>

                    <!-- CTA Buttons -->
                    <div class="pt-4 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                        <a href="#books-showcase" 
                           class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-sm sm:text-base shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">
                            <span>🔎</span>
                            <span>ค้นหาหนังสือในระบบ</span>
                        </a>

                        @auth
                            <a href="{{ route('books.index') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-800 font-bold text-sm sm:text-base border border-slate-200 shadow-sm transition-all">
                                <span>📚</span>
                                <span>ดูหนังสือของฉัน</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-800 font-bold text-sm sm:text-base border border-slate-200 shadow-sm transition-all">
                                <span>🔐</span>
                                <span>เริ่มต้นสมัครสมาชิกฟรี</span>
                            </a>
                        @endauth
                    </div>

                    <!-- Quick Stats Pill Bar -->
                    <div class="pt-8 flex flex-wrap items-center justify-center gap-6 text-xs sm:text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span>หนังสือในระบบ <strong>{{ $totalBooks ?? 0 }}</strong> เล่ม</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-indigo-500 font-bold">✓</span>
                            <span>สมาชิกชุมชน <strong>{{ $totalUsers ?? 0 }}</strong> คน</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-purple-500 font-bold">✓</span>
                            <span>แลกเปลี่ยนสำเร็จแล้ว <strong>{{ $totalExchanges ?? 0 }}</strong> ครั้ง</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Background Decorative Blobs -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-tr from-indigo-200/40 to-purple-200/40 rounded-full blur-3xl -z-10 pointer-events-none"></div>
        </section>

        <!-- 3. จุดเด่น 3 อย่าง (Key Highlights) -->
        <section id="features" class="py-16 sm:py-24 bg-white border-y border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                        HIGHLIGHTS
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                        3 จุดเด่นสำคัญของระบบ BookCycle
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500 mt-2">
                        ออกแบบมาเพื่อมอบประสบการณ์การแลกเปลี่ยนหนังสือที่คุ้มค่า สะดวก และแม่นยำที่สุด
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    {{-- 1. หนังสือมือสอง --}}
                    <div class="bg-slate-50/80 rounded-3xl p-8 border border-slate-200/80 hover:border-indigo-300 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform mb-6 shadow-xs">
                                📚
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">
                                1. หนังสือมือสองคุณภาพดี
                            </h3>
                            <p class="text-sm text-slate-600 mt-3 leading-relaxed">
                                เปลี่ยนหนังสือที่อ่านจบแล้วให้มีคุณค่าต่อผู้อื่น ประหยัดค่าใช้จ่ายในการซื้อหนังสือใหม่ และช่วยส่งเสริมวัฒนธรรมการอ่านที่ยั่งยืน
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-semibold text-indigo-600">
                            <span>หมุนเวียนหนังสือไม่รู้จบ</span>
                        </div>
                    </div>

                    {{-- 2. Smart Matching --}}
                    <div class="bg-slate-50/80 rounded-3xl p-8 border border-slate-200/80 hover:border-indigo-300 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform mb-6 shadow-xs">
                                🎯
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">
                                2. Smart Matching อัจฉริยะ
                            </h3>
                            <p class="text-sm text-slate-600 mt-3 leading-relaxed">
                                ระบบจับคู่สองทาง (2-Way Match) อัตโนมัติ เมื่อหนังสือที่คุณมีตรงกับสิ่งที่สมาชิกคนอื่นตามหา และเขามีเล่มที่คุณอยากได้ ระบบจะแจ้งเตือนให้ทันที
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-semibold text-amber-600">
                            <span>จับคู่ตรงความต้องการ 100%</span>
                        </div>
                    </div>

                    {{-- 3. การแลกเปลี่ยนแบบหมุนเวียน --}}
                    <div class="bg-slate-50/80 rounded-3xl p-8 border border-slate-200/80 hover:border-indigo-300 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform mb-6 shadow-xs">
                                🔄
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">
                                3. การแลกเปลี่ยนแบบหมุนเวียน
                            </h3>
                            <p class="text-sm text-slate-600 mt-3 leading-relaxed">
                                ไม่มีค่าธรรมเนียมแอบแฝง ส่งคำขอ ตอบรับ หรือปฏิเสธได้ง่ายดายในคลิกเดียว พร้อมสถานะการติดตามที่โปร่งใสและปลอดภัย
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center text-xs font-semibold text-emerald-600">
                            <span>ฟรีและปลอดภัยสำหรับทุกคน</span>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 4. How It Works (ขั้นตอนการใช้งาน) -->
        <section id="how-it-works" class="py-16 sm:py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                        HOW IT WORKS
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                        เริ่มต้นแลกหนังสือใน 3 ขั้นตอนง่ายๆ
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500 mt-2">
                        ไม่ซับซ้อน เริ่มต้นแลกหนังสือเล่มใหม่ได้ในเวลาไม่ถึง 2 นาที
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                    
                    {{-- Step 1 --}}
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center relative">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white font-extrabold text-lg flex items-center justify-center mx-auto mb-4 shadow-md shadow-indigo-500/20">
                            1
                        </div>
                        <h4 class="font-bold text-lg text-slate-800">ลงหนังสือที่คุณมี</h4>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                            เพิ่มชื่อหนังสือ สภาพ และรูปถ่ายของหนังสือที่คุณอ่านจบแล้วและพร้อมส่งต่อ
                        </p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center relative">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white font-extrabold text-lg flex items-center justify-center mx-auto mb-4 shadow-md shadow-amber-500/20">
                            2
                        </div>
                        <h4 class="font-bold text-lg text-slate-800">ระบุหนังสือที่ตามหา</h4>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                            ใส่ชื่อหนังสือหรือนักเขียนที่คุณอยากอ่านลงในรายการ "หนังสือที่ต้องการ"
                        </p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center relative">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white font-extrabold text-lg flex items-center justify-center mx-auto mb-4 shadow-md shadow-emerald-500/20">
                            3
                        </div>
                        <h4 class="font-bold text-lg text-slate-800">ระบบจับคู่ & แลกเปลี่ยน</h4>
                        <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                            เมื่อเจอบัญชีที่ต้องการตรงกัน กดส่งคำขอและรออีกฝ่ายตอบรับได้ทันที!
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- 5. หนังสือในระบบ (Books Showcase) -->
        <section id="books-showcase" class="py-16 sm:py-24 bg-white border-t border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                            EXPLORE BOOKS
                        </span>
                        <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                            หนังสือที่พร้อมให้แลกเปลี่ยนในระบบ
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            ตัวอย่างหนังสือจากเพื่อนสมาชิกที่พร้อมส่งต่อให้คุณ
                        </p>
                    </div>

                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-indigo-600 hover:text-indigo-800">
                        <span>สมัครสมาชิกเพื่อเริ่มแลก</span> <span>→</span>
                    </a>
                </div>

                @if(isset($featuredBooks) && $featuredBooks->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                        @foreach($featuredBooks as $fBook)
                            <div class="bg-slate-50/70 rounded-3xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col group">
                                
                                {{-- Cover --}}
                                <div class="relative aspect-[3/4] bg-slate-100 overflow-hidden">
                                    @if($fBook->image)
                                        <img src="{{ asset('storage/' . $fBook->image) }}" 
                                             alt="{{ $fBook->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-slate-100 text-slate-400">
                                            <span class="text-4xl">📖</span>
                                            <span class="text-[10px] text-slate-400 mt-1 font-medium">ไม่มีรูปปก</span>
                                        </div>
                                    @endif

                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-500 text-white text-[10px] font-bold shadow-xs">
                                            พร้อมแลก
                                        </span>
                                    </div>

                                    @if($fBook->condition)
                                        <div class="absolute top-2 left-2">
                                            <span class="px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-xs text-white text-[10px] font-medium">
                                                {{ $fBook->condition }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="p-4 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-1 leading-snug" title="{{ $fBook->title }}">
                                            {{ $fBook->title }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                            {{ $fBook->author ?? 'ไม่ระบุผู้แต่ง' }}
                                        </p>
                                    </div>

                                    <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500 truncate">
                                            👤 {{ $fBook->user->name ?? 'สมาชิก' }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-12 text-center text-slate-500">
                        <div class="text-5xl mb-3">📚</div>
                        <h3 class="font-bold text-base text-slate-700">ร่วมลงหนังสือเล่มแรกในระบบ</h3>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">สมัครสมาชิกวันนี้และเริ่มแลกเปลี่ยนหนังสือกับเพื่อนๆ</p>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-xs sm:text-sm font-semibold shadow-sm">
                            สมัครสมาชิกฟรี
                        </a>
                    </div>
                @endif

            </div>
        </section>

        <!-- 6. Call to Action Banner -->
        <section class="py-16 sm:py-20 bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 text-white text-center">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                    พร้อมเริ่มต้นส่งต่อหนังสือเล่มโปรดของคุณแล้วหรือยัง?
                </h2>
                <p class="text-sm sm:text-base text-indigo-100 max-w-2xl mx-auto">
                    เข้าร่วมคอมมูนิตี้คนรักการอ่าน ค้นพบหนังสือเล่มใหม่ และแลกเปลี่ยนความรู้ได้ฟรีวันนี้
                </p>
                <div class="pt-2">
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-white text-indigo-700 font-extrabold text-sm sm:text-base shadow-xl hover:bg-indigo-50 hover:scale-105 active:scale-95 transition-all">
                        <span>🚀 สมัครสมาชิกและเริ่มแลกเปลี่ยน</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 7. Footer -->
        <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white text-xl">
                            📚
                        </div>
                        <div>
                            <span class="font-bold text-lg text-white">BookCycle</span>
                            <p class="text-xs text-slate-500">ระบบแลกเปลี่ยนหนังสือมือสองแบบหมุนเวียน</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 text-xs sm:text-sm">
                        <a href="{{ route('login') }}" class="hover:text-white transition-colors">เข้าสู่ระบบ</a>
                        <a href="{{ route('register') }}" class="hover:text-white transition-colors">สมัครสมาชิก</a>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-800 text-center text-xs text-slate-500">
                    © {{ date('Y') }} BookCycle Platform. All rights reserved.
                </div>
            </div>
        </footer>

    </body>
</html>
