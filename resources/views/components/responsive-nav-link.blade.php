@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-start text-base font-semibold text-indigo-600 bg-indigo-50 border-l-4 border-indigo-600 transition-all duration-150 ease-in-out'
            : 'flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-start text-base font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

