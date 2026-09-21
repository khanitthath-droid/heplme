@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold text-indigo-600 bg-indigo-50/90 shadow-xs border border-indigo-100/80 transition-all duration-150 ease-in-out'
            : 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-all duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

