<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
            @include('layouts.navigation')

            <div class="flex flex-1 overflow-hidden">
                <!-- Optional Sidebar -->
                @isset($sidebar)
                    {{ $sidebar }}
                @endisset

                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white dark:bg-gray-800 shadow flex-shrink-0">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main class="flex-1 overflow-auto">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        @auth
        <!-- Feedback Button and Modal -->
        <div x-data="feedbackWidget()" x-cloak>
            <!-- Floating Feedback Button -->
            <button
                @click="openModal()"
                class="fixed bottom-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-full shadow-lg flex items-center gap-2 transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 z-40"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <span class="font-medium">Give Feedback</span>
            </button>

            <!-- Feedback Modal -->
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="feedback-modal-title"
                role="dialog"
                aria-modal="true"
            >
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="closeModal()"></div>

                <!-- Modal panel -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        x-show="isOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                        @click.stop
                    >
                        <!-- Close button -->
                        <div class="absolute right-0 top-0 pr-4 pt-4">
                            <button
                                type="button"
                                @click="closeModal()"
                                class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal content -->
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100" id="feedback-modal-title">
                                    Give Feedback
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    We value your feedback! Let us know how we can improve.
                                </p>

                                <!-- Success message -->
                                <div x-show="submitted" x-transition class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <p class="ml-2 text-sm font-medium text-green-700 dark:text-green-300">Feedback submitted successfully!</p>
                                    </div>
                                </div>

                                <!-- Error message -->
                                <div x-show="error" x-transition class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <p class="ml-2 text-sm font-medium text-red-700 dark:text-red-300" x-text="error"></p>
                                    </div>
                                </div>

                                <!-- Feedback form -->
                                <form x-show="!submitted" @submit.prevent="submitFeedback()" class="mt-4 space-y-4">
                                    <div>
                                        <label for="feedback-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Your Email
                                        </label>
                                        <input
                                            type="email"
                                            id="feedback-email"
                                            x-model="email"
                                            required
                                            placeholder="your@email.com"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        >
                                    </div>

                                    <div>
                                        <label for="feedback-message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Your Feedback
                                        </label>
                                        <textarea
                                            id="feedback-message"
                                            x-model="message"
                                            required
                                            rows="4"
                                            maxlength="500"
                                            placeholder="Tell us what you think..."
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span x-text="message.length"></span>/500 characters
                                        </p>
                                    </div>

                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button
                                            type="submit"
                                            :disabled="submitting"
                                            class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:ml-3 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span x-show="!submitting">Submit</span>
                                            <span x-show="submitting" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Submitting...
                                            </span>
                                        </button>
                                        <button
                                            type="button"
                                            @click="closeModal()"
                                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>

                                <!-- Close button after submission -->
                                <div x-show="submitted" class="mt-5 sm:mt-4">
                                    <button
                                        type="button"
                                        @click="closeModal()"
                                        class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    >
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function feedbackWidget() {
                return {
                    isOpen: false,
                    email: '{{ Auth::user()->email ?? '' }}',
                    message: '',
                    submitting: false,
                    submitted: false,
                    error: null,

                    openModal() {
                        this.isOpen = true;
                        this.submitted = false;
                        this.error = null;
                    },

                    closeModal() {
                        this.isOpen = false;
                        if (this.submitted) {
                            this.message = '';
                            this.submitted = false;
                        }
                        this.error = null;
                    },

                    async submitFeedback() {
                        this.submitting = true;
                        this.error = null;

                        try {
                            const response = await window.axios.post('/submit-feedback', {
                                email: this.email,
                                message: this.message
                            });

                            this.submitted = true;
                        } catch (err) {
                            if (err.response && err.response.data && err.response.data.errors) {
                                const errors = err.response.data.errors;
                                this.error = Object.values(errors).flat().join(' ');
                            } else {
                                this.error = 'An error occurred. Please try again.';
                            }
                        } finally {
                            this.submitting = false;
                        }
                    }
                };
            }
        </script>
        @endauth
    </body>
</html>
