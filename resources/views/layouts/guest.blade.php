<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TaskFlow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Header -->
        <header class="w-full py-6 px-6 lg:px-8 bg-gray-100 dark:bg-gray-900">
            <nav class="max-w-7xl mx-auto">
                <a href="{{ route('home') }}" class="flex items-center gap-2 w-fit">
                    <svg class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 2C8.45 2 8 2.45 8 3V4H6C4.9 4 4 4.9 4 6V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V6C20 4.9 19.1 4 18 4H16V3C16 2.45 15.55 2 15 2H9ZM9 3H15V5H9V3ZM6 6H8V6.5C8 6.78 8.22 7 8.5 7H15.5C15.78 7 16 6.78 16 6.5V6H18V20H6V6Z"/>
                        <path d="M8.5 10.5L10 12L13.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <path d="M15 10H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M8.5 14.5L10 16L13.5 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <path d="M15 14H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">TaskFlow</span>
                </a>
            </nav>
        </header>

        <div class="min-h-[calc(100vh-88px)] flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
