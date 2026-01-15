<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Feedback & Feature Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Status Summary -->
            @if ($featureRequests->count() > 0)
                @php
                    $statusCounts = [
                        'pending' => $featureRequests->where('status', 'pending')->count(),
                        'processing' => $featureRequests->where('status', 'processing')->count(),
                        'completed' => $featureRequests->whereIn('status', ['completed', 'failed'])->count(),
                    ];
                @endphp
                <div class="grid grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg text-center">
                        <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $statusCounts['pending'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Not Acknowledged</div>
                    </div>
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statusCounts['processing'] }}</div>
                        <div class="text-sm text-blue-700 dark:text-blue-300">In Process</div>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statusCounts['completed'] }}</div>
                        <div class="text-sm text-green-700 dark:text-green-300">Ready for Approval</div>
                    </div>
                </div>
            @endif

            <!-- Submit New Request Form -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Submit a New Request') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Share your feedback, request a feature, or report a bug. Our AI will review your submission and may create an issue to address it.') }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('feedback.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="feature_request" {{ old('type') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                                    <option value="feedback" {{ old('type') === 'feedback' ? 'selected' : '' }}>Feedback</option>
                                    <option value="bug_report" {{ old('type') === 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type')" />
                            </div>

                            <div>
                                <x-input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('priority')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required placeholder="Brief summary of your request" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required placeholder="Provide as much detail as possible...">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>{{ __('Submit Request') }}</x-primary-button>
                        </div>
                    </form>
                </section>
            </div>

            <!-- Previous Submissions -->
            @if ($featureRequests->count() > 0)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Your Submissions') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Track the progress of your feedback and feature requests below.
                            </p>
                        </header>

                        <div class="mt-6 space-y-4">
                            @foreach ($featureRequests as $request)
                                @php
                                    $typeColors = [
                                        'feature_request' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'feedback' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                        'bug_report' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                    ];
                                    $statusConfig = [
                                        'pending' => [
                                            'label' => 'Not Acknowledged',
                                            'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                            'description' => 'Your request has been received and is waiting to be picked up.',
                                        ],
                                        'processing' => [
                                            'label' => 'In Process',
                                            'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'icon' => '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>',
                                            'description' => 'A worker is currently processing your request.',
                                        ],
                                        'completed' => [
                                            'label' => 'Ready for Approval',
                                            'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                            'description' => 'PR created and waiting for your approval.',
                                        ],
                                        'failed' => [
                                            'label' => 'Ready for Approval',
                                            'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                            'description' => 'Processing completed - please review.',
                                        ],
                                    ];
                                    $status = $statusConfig[$request->status] ?? $statusConfig['pending'];
                                @endphp
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                                    <!-- Status Banner -->
                                    <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-100 dark:border-gray-700">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full {{ $status['color'] }}">
                                            {!! $status['icon'] !!}
                                            {{ $status['label'] }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $status['description'] }}
                                        </span>
                                    </div>

                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $request->title }}
                                                </h3>
                                                <span class="px-2 py-0.5 text-xs font-medium rounded {{ $typeColors[$request->type] ?? '' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                                </span>
                                                @if ($request->priority === 'high')
                                                    <span class="px-2 py-0.5 text-xs font-medium rounded bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                        High Priority
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                {{ Str::limit($request->description, 200) }}
                                            </p>

                                            <div class="mt-3 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $request->user->name }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Submitted {{ $request->created_at->diffForHumans() }}
                                                </span>
                                                @if ($request->processed_at)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Processed {{ $request->processed_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($request->github_pr_url)
                                                <a href="{{ $request->github_pr_url }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-gray-800 dark:bg-gray-700 rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                                    </svg>
                                                    View on GitHub
                                                </a>
                                            @endif

                                            @if ($request->ai_response && $request->isCompleted())
                                                <details class="mt-3">
                                                    <summary class="cursor-pointer text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                                        View AI Analysis
                                                    </summary>
                                                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
                                                        {!! nl2br(e(Str::limit($request->ai_response, 1000))) !!}
                                                    </div>
                                                </details>
                                            @endif
                                        </div>

                                        @if ($request->isPending())
                                            <form method="post" action="{{ route('feedback.destroy', $request) }}" class="ml-4" onsubmit="return confirm('Are you sure you want to delete this request?')">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Delete request">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
            @else
                <!-- Empty State -->
                <div class="p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No submissions yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by submitting your first feedback or feature request above.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
