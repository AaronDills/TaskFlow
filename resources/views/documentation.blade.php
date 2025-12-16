<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Documentation - {{ config('app.name', 'TaskFlow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col" x-data="{ activeSection: 'daily-schedule' }">
            <!-- Header -->
            <header class="w-full py-4 px-6 lg:px-8 bg-white dark:bg-gray-800 shadow flex-shrink-0">
                <nav class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <svg class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 2C8.45 2 8 2.45 8 3V4H6C4.9 4 4 4.9 4 6V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V6C20 4.9 19.1 4 18 4H16V3C16 2.45 15.55 2 15 2H9ZM9 3H15V5H9V3ZM6 6H8V6.5C8 6.78 8.22 7 8.5 7H15.5C15.78 7 16 6.78 16 6.5V6H18V20H6V6Z"/>
                                <path d="M8.5 10.5L10 12L13.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                <path d="M15 10H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M8.5 14.5L10 16L13.5 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                <path d="M15 14H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">TaskFlow</span>
                        </a>
                    </div>
                    @if (Route::has('login'))
                        <div class="flex items-center gap-4">
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

            <!-- Main Layout: Sidebar + Content -->
            <div class="flex-1 flex flex-row overflow-hidden">
                <!-- Sidebar - Left side on desktop -->
                <aside id="doc-sidebar" class="w-64 bg-gray-800 dark:bg-gray-900 text-white flex-col hidden md:flex">
                    <nav class="flex-1 py-4">
                        <!-- Daily Schedule -->
                        <button @click="activeSection = 'daily-schedule'"
                                :class="activeSection === 'daily-schedule' ? 'bg-gray-700 border-l-4 border-blue-500' : 'hover:bg-gray-700'"
                                class="w-full flex items-center px-4 py-3 transition-colors">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="ml-3">Daily Schedule</span>
                        </button>

                        <!-- Projects & Labels -->
                        <button @click="activeSection = 'projects'"
                                :class="activeSection === 'projects' ? 'bg-gray-700 border-l-4 border-blue-500' : 'hover:bg-gray-700'"
                                class="w-full flex items-center px-4 py-3 transition-colors">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span class="ml-3">Projects & Labels</span>
                        </button>

                        <!-- Tasks & Subtasks -->
                        <button @click="activeSection = 'tasks'"
                                :class="activeSection === 'tasks' ? 'bg-gray-700 border-l-4 border-blue-500' : 'hover:bg-gray-700'"
                                class="w-full flex items-center px-4 py-3 transition-colors">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="ml-3">Tasks & Subtasks</span>
                        </button>

                        <!-- Calendar -->
                        <button @click="activeSection = 'calendar'"
                                :class="activeSection === 'calendar' ? 'bg-gray-700 border-l-4 border-blue-500' : 'hover:bg-gray-700'"
                                class="w-full flex items-center px-4 py-3 transition-colors">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="ml-3">Calendar</span>
                        </button>

                        <!-- History -->
                        <button @click="activeSection = 'history'"
                                :class="activeSection === 'history' ? 'bg-gray-700 border-l-4 border-blue-500' : 'hover:bg-gray-700'"
                                class="w-full flex items-center px-4 py-3 transition-colors">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="ml-3">History</span>
                        </button>

                        <!-- Focus Timer -->
                        <button @click="activeSection = 'timer'"
                                :class="activeSection === 'timer' ? 'bg-gray-700 border-l-4 border-blue-500' : 'hover:bg-gray-700'"
                                class="w-full flex items-center px-4 py-3 transition-colors">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="ml-3">Focus Timer</span>
                        </button>
                    </nav>
                </aside>

                <!-- Main Content Area -->
                <main class="flex-1 flex flex-col overflow-hidden">
                    <!-- Mobile Navigation (shown at top on small screens) -->
                    <div class="md:hidden bg-gray-800 dark:bg-gray-900 border-b border-gray-700 p-3 overflow-x-auto flex-shrink-0">
                        <div class="flex gap-2 min-w-max">
                            <button @click="activeSection = 'daily-schedule'"
                                    :class="activeSection === 'daily-schedule' ? 'bg-blue-600 text-white' : 'text-gray-300 bg-gray-700'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                                Schedule
                            </button>
                            <button @click="activeSection = 'projects'"
                                    :class="activeSection === 'projects' ? 'bg-blue-600 text-white' : 'text-gray-300 bg-gray-700'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                                Projects
                            </button>
                            <button @click="activeSection = 'tasks'"
                                    :class="activeSection === 'tasks' ? 'bg-blue-600 text-white' : 'text-gray-300 bg-gray-700'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                                Tasks
                            </button>
                            <button @click="activeSection = 'calendar'"
                                    :class="activeSection === 'calendar' ? 'bg-blue-600 text-white' : 'text-gray-300 bg-gray-700'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                                Calendar
                            </button>
                            <button @click="activeSection = 'history'"
                                    :class="activeSection === 'history' ? 'bg-blue-600 text-white' : 'text-gray-300 bg-gray-700'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                                History
                            </button>
                            <button @click="activeSection = 'timer'"
                                    :class="activeSection === 'timer' ? 'bg-blue-600 text-white' : 'text-gray-300 bg-gray-700'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                                Timer
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="flex-1 overflow-auto p-6 lg:p-10">
                    <div class="max-w-3xl">
                        <!-- Daily Schedule Section -->
                        <div x-show="activeSection === 'daily-schedule'" x-cloak>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Daily Schedule</h1>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    The Daily Schedule is your central hub for planning and managing your day. It displays a time-based calendar alongside your task lists.
                                </p>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Time Calendar</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>View your day in 15-minute time slots from 8 AM to 6 PM</li>
                                    <li>Events from your calendar appear as colored overlays on the time slots</li>
                                    <li>Navigate between weekdays using the arrow buttons at the top</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Task Lists</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li><strong>Must Do:</strong> High-priority tasks that need to be completed today</li>
                                    <li><strong>May Do:</strong> Lower-priority tasks you can work on if time permits</li>
                                    <li><strong>Recommended:</strong> Smart suggestions from your projects based on priority and due dates</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Managing Tasks</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Click the checkbox to mark a task as complete</li>
                                    <li>Click on a task name to edit it inline</li>
                                    <li>Use the "Add task" input at the bottom of each list to create new tasks</li>
                                    <li>Drag and drop tasks to reorder them within a list</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Projects & Labels Section -->
                        <div x-show="activeSection === 'projects'" x-cloak>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Projects & Labels</h1>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    Projects help you organize related tasks together. Each project can contain multiple parent tasks and subtasks.
                                </p>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Creating a Project</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Click the "New Project" button on the Projects page</li>
                                    <li>Enter a name and optional description</li>
                                    <li>Choose a status: Ready to Begin, In Progress, On Hold, or Done</li>
                                    <li>Optionally assign a label for color-coding</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Project Cards</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Each project displays as a card showing its name, description, and status</li>
                                    <li>The task count shows how many tasks are in the project</li>
                                    <li>Click on a project card to view and manage its tasks</li>
                                    <li>Use the three-dot menu for quick actions like edit or delete</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Project Status</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li><strong>Ready to Begin:</strong> Project is set up but work hasn't started</li>
                                    <li><strong>In Progress:</strong> Actively working on this project</li>
                                    <li><strong>On Hold:</strong> Temporarily paused</li>
                                    <li><strong>Done:</strong> Project is complete</li>
                                </ul>

                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-10 mb-4">Labels</h2>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    Labels help you categorize and color-code your projects for easy visual organization.
                                </p>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Creating Labels</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Go to the Projects page and click the "Labels" tab</li>
                                    <li>Click "New Label" to create a new label</li>
                                    <li>Enter a name and select a color</li>
                                    <li>Labels can be reused across multiple projects</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Using Labels</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Assign labels to projects when creating or editing them</li>
                                    <li>Project cards display their assigned label color</li>
                                    <li>Use labels to group related projects (e.g., "Work", "Personal", "Side Projects")</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Managing Labels</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Edit labels to change their name or color</li>
                                    <li>View how many projects use each label</li>
                                    <li>Delete labels you no longer need (projects will become unlabeled)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tasks & Subtasks Section -->
                        <div x-show="activeSection === 'tasks'" x-cloak>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Tasks & Subtasks</h1>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    Break down your projects into manageable pieces using parent tasks and subtasks.
                                </p>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Parent Tasks</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Parent tasks represent major milestones or phases of your project</li>
                                    <li>Create a parent task by clicking "Add Parent Task" within a project</li>
                                    <li>Each parent task can contain multiple subtasks</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Subtasks</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Subtasks are the individual action items within a parent task</li>
                                    <li>Set priority (High, Medium, Low) to help with daily recommendations</li>
                                    <li>Add due dates to ensure timely completion</li>
                                    <li>Mark subtasks complete by clicking the checkbox</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Priority Levels</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li><strong>High:</strong> Urgent tasks that should be done first</li>
                                    <li><strong>Medium:</strong> Important but not urgent tasks</li>
                                    <li><strong>Low:</strong> Tasks that can wait if needed</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Smart Recommendations</h3>
                                <p class="text-gray-600 dark:text-gray-300">
                                    The system automatically recommends subtasks on your Daily Schedule based on their priority and due dates, helping you focus on what matters most.
                                </p>
                            </div>
                        </div>

                        <!-- Calendar Section -->
                        <div x-show="activeSection === 'calendar'" x-cloak>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Calendar</h1>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    The Calendar helps you schedule events, appointments, and time-blocked activities.
                                </p>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Viewing the Calendar</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>The calendar displays a monthly view by default</li>
                                    <li>Navigate between months using the arrow buttons</li>
                                    <li>Days with events show colored indicators</li>
                                    <li>Click on any day to see or add events</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Creating Events</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Click "New Event" or click on a day to create an event</li>
                                    <li>Enter the event title and description</li>
                                    <li>Set the start and end time</li>
                                    <li>Choose a color to visually categorize your events</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Recurring Events</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Set events to repeat daily, weekly, or monthly</li>
                                    <li>Recurring events appear on all applicable days</li>
                                    <li>Edit or delete individual occurrences or the entire series</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Calendar Integration</h3>
                                <p class="text-gray-600 dark:text-gray-300">
                                    Events from your calendar automatically appear on the Daily Schedule time view, helping you plan your tasks around your commitments.
                                </p>
                            </div>
                        </div>

                        <!-- History Section -->
                        <div x-show="activeSection === 'history'" x-cloak>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">History</h1>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    Track your productivity over time and celebrate your accomplishments with the History view.
                                </p>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Weekly View</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>See completed tasks organized by week</li>
                                    <li>Navigate between weeks using the arrow buttons</li>
                                    <li>Each day shows the tasks you completed</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Productivity Insights</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>View your total completed tasks for the week</li>
                                    <li>See your completion rate and trends</li>
                                    <li>Identify your most productive days</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Staying Motivated</h3>
                                <p class="text-gray-600 dark:text-gray-300">
                                    Use the History view to reflect on your progress, identify patterns in your productivity, and stay motivated by seeing how much you've accomplished over time.
                                </p>
                            </div>
                        </div>

                        <!-- Focus Timer Section -->
                        <div x-show="activeSection === 'timer'" x-cloak>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Focus Timer</h1>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    The built-in Focus Timer helps you stay concentrated on your tasks using timed work sessions.
                                </p>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Using the Timer</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Find the timer in the sidebar on any page</li>
                                    <li>Select your desired duration: 5, 15, 30, or 60 minutes</li>
                                    <li>Click "Start" to begin your focus session</li>
                                    <li>The timer continues even if you navigate to other pages</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Timer Controls</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li><strong>Start:</strong> Begin the countdown</li>
                                    <li><strong>Pause:</strong> Temporarily stop the timer</li>
                                    <li><strong>Stop:</strong> End the session and reset the timer</li>
                                </ul>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3">Tips for Focus Sessions</h3>
                                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                                    <li>Start with shorter sessions (15 minutes) and work up to longer ones</li>
                                    <li>Take a short break between sessions</li>
                                    <li>Focus on a single task during each session</li>
                                    <li>Minimize distractions by closing unnecessary tabs and apps</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    </div>
                </main>
            </div>
        </div>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </body>
</html>
