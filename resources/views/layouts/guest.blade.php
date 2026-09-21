<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BookCycle - ระบบแลกเปลี่ยนหนังสือมือสองแบบหมุนเวียน') }}</title>

        <!-- Fonts (Prompt & Plus Jakarta Sans) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-theme-user min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white relative overflow-x-hidden">
        
        <!-- Ambient Background Glow Orbs -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
            <div class="absolute -top-32 -left-32 w-80 h-80 bg-indigo-400/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-32 w-96 h-96 bg-purple-400/15 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 left-1/4 w-80 h-80 bg-sky-400/15 rounded-full blur-3xl"></div>
        </div>

        <!-- Header / Back to home -->
        <header class="py-5 px-6 flex justify-between items-center max-w-7xl mx-auto w-full relative z-10">
            <a href="/" class="group flex items-center gap-2">
                <x-application-logo />
            </a>

            <a href="/" class="text-xs sm:text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/60 hover:bg-white border border-slate-200/60 shadow-2xs backdrop-blur-xs">
                <span>←</span> <span>กลับสู่หน้าแรก</span>
            </a>
        </header>

        <!-- Main Content (Centered Form Card) -->
        <main class="flex-1 flex flex-col justify-center items-center px-4 py-8 sm:py-12 relative z-10">
            <div class="w-full sm:max-w-md bg-white/90 backdrop-blur-xl p-7 sm:p-9 rounded-3xl border border-slate-200/90 shadow-xl shadow-indigo-500/5">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 text-center text-xs text-slate-400 border-t border-slate-200/60 bg-white/60 backdrop-blur-xs relative z-10">
            <div class="max-w-7xl mx-auto px-4">
                © {{ date('Y') }} BookCycle Platform - ระบบแลกเปลี่ยนหนังสือมือสองแบบหมุนเวียน
            </div>
        </footer>

    </body>
</html>
