@props(['type'])
@php
    $bgColor = '';
    switch ($type) {
        case 'success':
            $bgColor = 'text-green-800 bg-green-50 dark:text-green-400';
            break;
        case 'danger':
            $bgColor = 'text-red-800 bg-red-50 dark:text-red-400';
            break;
        case 'warning':
            $bgColor = 'text-yellow-800 bg-yellow-50 dark:text-yellow-300';
            break;
    }
@endphp

<div class="flex items-center p-4 mt-1 mb-4 rounded-lg dark:bg-gray-800 {{$bgColor}}" role="alert">
    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
    </svg>
    <span class="sr-only">Info</span>
    <div class="ms-3 text-sm font-medium">
        {{$slot}}
    </div>
</div>
