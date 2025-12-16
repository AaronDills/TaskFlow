<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'TaskFlow') }} - Organize Your Work, Amplify Your Focus</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white dark:bg-gray-900 font-sans antialiased">
        <!-- Header -->
        <header class="w-full py-6 px-6 lg:px-8">
            <nav class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 2C8.45 2 8 2.45 8 3V4H6C4.9 4 4 4.9 4 6V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V6C20 4.9 19.1 4 18 4H16V3C16 2.45 15.55 2 15 2H9ZM9 3H15V5H9V3ZM6 6H8V6.5C8 6.78 8.22 7 8.5 7H15.5C15.78 7 16 6.78 16 6.5V6H18V20H6V6Z"/>
                        <path d="M8.5 10.5L10 12L13.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <path d="M15 10H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M8.5 14.5L10 16L13.5 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <path d="M15 14H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">TaskFlow</span>
                </div>
                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        <a href="{{ route('guide') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            Guide
                        </a>
                        @auth
                            <a href="{{ url('/todo') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                Dashboard
                            </a>
                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none transition">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 py-1 z-50"
                                     style="display: none;">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                    Get Started Free
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="pt-20 pb-10 px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                    Organize Your Work,<br>
                    <span class="text-blue-600">Amplify Your Focus</span>
                </h1>

                <p class="mt-6 text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    The smart task management system that helps you prioritize what matters, schedule your day, and track your progress over time.
                </p>

            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 px-6 lg:px-8">
            <div class="max-w-7xl mx-auto bg-gray-50 dark:bg-gray-800 p-10 lg:p-16" style="border-radius: 2rem;">
                <div class="text-center mb-14 lg:mb-20 px-4 lg:px-8 pt-4">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                        Everything you need to stay productive
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                        Powerful features designed to help you work smarter, not harder.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-10">
                    <!-- Feature 1: Projects -->
                    <div class="p-6">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center mb-6" style="border-radius: 1rem;">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Project Organization</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Group related tasks into projects with color-coded labels. Break down complex work into manageable parent tasks and subtasks.
                        </p>
                    </div>

                    <!-- Feature 2: Smart Scheduling -->
                    <div class="p-6">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 flex items-center justify-center mb-6" style="border-radius: 1rem;">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Daily Schedule</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Plan your day with lists. Get smart recommendations based on priority and deadlines to focus on what matters most.
                        </p>
                    </div>

                    <!-- Feature 3: Calendar -->
                    <div class="p-6">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center mb-6" style="border-radius: 1rem;">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Event Calendar</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Schedule events and appointments with a monthly calendar. Set recurring events and color-code your commitments.
                        </p>
                    </div>

                    <!-- Feature 4: History & Analytics -->
                    <div class="p-6">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/50 flex items-center justify-center mb-6" style="border-radius: 1rem;">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Progress History</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Track your productivity over time with weekly views of completed tasks. See your streak, completion rates, and celebrate your wins.
                        </p>
                    </div>

                    <!-- Feature 5: Priority System -->
                    <div class="p-6">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/50 flex items-center justify-center mb-6" style="border-radius: 1rem;">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Smart Priorities</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Set high, medium, or low priority on tasks. The system automatically surfaces urgent items based on due dates and importance.
                        </p>
                    </div>

                    <!-- Feature 6: Focus Timer -->
                    <div class="p-6">
                        <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/50 flex items-center justify-center mb-6" style="border-radius: 1rem;">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Focus Timer</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Built-in timer to help you focus on tasks. Set 5, 15, 30, or 60-minute work sessions and stay in the zone.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                    Ready to get organized?
                </h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                    Join the growing number of productive people who trust TaskFlow to manage their work.
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 px-6 lg:px-8 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto text-center text-gray-500 dark:text-gray-400 text-sm">
                Built for productivity. Written by Aaron Dills.
            </div>
        </footer>
    </body>
</html>
