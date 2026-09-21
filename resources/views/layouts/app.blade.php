<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BookCycle - ระบบแลกเปลี่ยนหนังสือมือสองแบบหมุนเวียน') }}</title>

        <!-- Fonts (Prompt for Thai & English) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 {{ Auth::check() && Auth::user()->role === 'admin' ? 'bg-theme-admin' : 'bg-theme-user' }} min-h-screen selection:bg-indigo-500 selection:text-white relative">
        
        <!-- Ambient Decorative Mesh Orbs (Fixed Backdrop) -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 -right-40 w-96 h-96 bg-indigo-400/15 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-fuchsia-400/15 rounded-full blur-3xl"></div>
            @else
                <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-400/15 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 -right-40 w-96 h-96 bg-sky-400/15 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl"></div>
            @endif
        </div>

        <div class="min-h-screen flex flex-col justify-between">
            <div>
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white/85 backdrop-blur-md border-b border-slate-200/80 sticky top-16 z-20 shadow-xs">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>

            <!-- Footer -->
            <footer class="bg-white/80 backdrop-blur-md border-t border-slate-200/80 py-6 mt-12 text-center text-sm text-slate-500">
                <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📚</span>
                        <span class="font-bold text-slate-700">BookCycle</span>
                        <span class="text-xs text-slate-400">| ระบบแลกเปลี่ยนหนังสือมือสองแบบหมุนเวียน</span>
                    </div>
                    <p class="text-xs text-slate-400">
                        © {{ date('Y') }} BookCycle Platform. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>
