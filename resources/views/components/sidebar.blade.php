@props(['projects' => collect()])

<aside id="app-sidebar" class="bg-gray-800 dark:bg-gray-900 text-white transition-all duration-300 flex flex-col"
       :class="sidebarExpanded ? 'w-64' : 'w-16'"
       x-data="{ sidebarExpanded: localStorage.getItem('sidebarExpanded') !== 'false' }"
       x-init="$watch('sidebarExpanded', val => localStorage.setItem('sidebarExpanded', val))">

    <!-- Toggle Button -->
    <div class="p-3 flex justify-end border-b border-gray-700">
        <button @click="sidebarExpanded = !sidebarExpanded"
                class="p-2 rounded-lg hover:bg-gray-700 transition-colors"
                :title="sidebarExpanded ? 'Collapse sidebar' : 'Expand sidebar'">
            <svg x-show="sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
            <svg x-show="!sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 py-4">
        <!-- To-Do Link -->
        <a href="{{ route('todo') }}"
           class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('todo') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}"
           :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span x-show="sidebarExpanded" x-transition class="ml-3">To-Do</span>
        </a>

        <!-- Projects Link -->
        <a href="{{ route('projects.index') }}"
           class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('projects.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}"
           :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="sidebarExpanded" x-transition class="ml-3">Projects</span>
        </a>

        <!-- Calendar Link -->
        <a href="{{ route('calendar') }}"
           class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('calendar') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}"
           :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span x-show="sidebarExpanded" x-transition class="ml-3">Calendar</span>
        </a>

        <!-- History Link -->
        <a href="{{ route('history') }}"
           class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('history') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}"
           :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="sidebarExpanded" x-transition class="ml-3">History</span>
        </a>

        <!-- Divider -->
        <div class="border-t border-gray-700 my-4 mx-4"></div>

        <!-- Timer Section -->
        <div class="px-4" x-data="timerWidget()">
            <!-- Timer Display (shown when timer is running or paused) -->
            <div x-show="isRunning || isPaused"
                 class="mb-3 text-center py-2 rounded-lg font-mono text-lg transition-all relative"
                 :class="[isPaused ? 'bg-yellow-600' : 'bg-green-600']">
                <div @click="togglePause()"
                     class="cursor-pointer"
                     :class="[isPaused ? 'animate-pulse' : 'hover:opacity-80']"
                     :title="isPaused ? 'Click to resume' : 'Click to pause'">
                    <span x-text="formatTime(remaining)"></span>
                    <span x-show="isPaused" class="block text-xs mt-1 font-sans">Paused</span>
                </div>
                <!-- Cancel button (shown when paused) -->
                <button x-show="isPaused"
                        @click.stop="stopTimer()"
                        class="absolute p-1 rounded-full hover:bg-yellow-700 transition"
                        style="top: 4px; right: 4px;"
                        title="Cancel timer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Timer Button/Dropdown -->
            <div class="relative" x-data="{ timerOpen: false }">
                <button @click="timerOpen = !timerOpen"
                        class="w-full flex items-center px-3 py-3 rounded-lg hover:bg-gray-700 transition-colors"
                        :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
                    <svg class="w-6 h-6 flex-shrink-0" :class="isRunning ? 'text-green-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-show="sidebarExpanded" x-transition class="ml-3">Timer</span>
                    <svg x-show="sidebarExpanded" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Timer Dropdown -->
                <div x-show="timerOpen"
                     @click.away="timerOpen = false"
                     x-transition
                     class="absolute left-0 mt-1 w-48 bg-gray-700 rounded-lg shadow-lg py-2 z-50"
                     :class="sidebarExpanded ? '' : 'left-full ml-2 -mt-12'">
                    <div class="px-3 py-1 text-xs text-gray-400 font-semibold">Start Timer</div>
                    <button @click="startTimer(5); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm">5 minutes</button>
                    <button @click="startTimer(10); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm">10 minutes</button>
                    <button @click="startTimer(15); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm">15 minutes</button>
                    <button @click="startTimer(30); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm">30 minutes</button>
                    <button @click="startTimer(60); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm">1 hour</button>
                    <template x-if="isRunning || isPaused">
                        <div>
                            <div class="border-t border-gray-600 my-1"></div>
                            <button x-show="isRunning" @click="togglePause(); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm text-yellow-400">Pause Timer</button>
                            <button x-show="isPaused" @click="togglePause(); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm text-green-400">Resume Timer</button>
                            <button @click="stopTimer(); timerOpen = false" class="w-full text-left px-3 py-2 hover:bg-gray-600 text-sm text-red-400">Stop Timer</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </nav>

    <!-- Projects Quick Access (when expanded) -->
    <div x-show="sidebarExpanded" x-transition class="border-t border-gray-700 py-4 px-4">
        <div class="text-xs text-gray-400 font-semibold mb-2">Recent Projects</div>
        @forelse($projects->take(5) as $project)
            <a href="{{ route('todo') }}?project={{ $project->hash }}"
               class="block py-2 px-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded truncate"
               title="{{ $project->name }}">
                {{ $project->name }}
            </a>
        @empty
            <p class="text-sm text-gray-500">No projects yet</p>
        @endforelse
    </div>
</aside>

<script>
function timerWidget() {
    return {
        isRunning: false,
        isPaused: false,
        remaining: 0,
        endTime: null,
        interval: null,

        init() {
            // Restore timer state from localStorage
            const savedEnd = localStorage.getItem('timerEndTime');
            const savedPaused = localStorage.getItem('timerPausedRemaining');

            if (savedPaused) {
                // Timer was paused
                this.remaining = parseInt(savedPaused);
                this.isPaused = true;
            } else if (savedEnd) {
                const end = parseInt(savedEnd);
                if (end > Date.now()) {
                    this.endTime = end;
                    this.isRunning = true;
                    this.tick();
                    this.interval = setInterval(() => this.tick(), 1000);
                } else {
                    localStorage.removeItem('timerEndTime');
                }
            }
        },

        startTimer(minutes) {
            this.stopTimer();
            this.endTime = Date.now() + (minutes * 60 * 1000);
            localStorage.setItem('timerEndTime', this.endTime);
            localStorage.removeItem('timerPausedRemaining');
            this.isRunning = true;
            this.isPaused = false;
            this.tick();
            this.interval = setInterval(() => this.tick(), 1000);
        },

        togglePause() {
            if (this.isRunning) {
                // Pause the timer
                if (this.interval) {
                    clearInterval(this.interval);
                    this.interval = null;
                }
                this.isRunning = false;
                this.isPaused = true;
                localStorage.removeItem('timerEndTime');
                localStorage.setItem('timerPausedRemaining', this.remaining);
            } else if (this.isPaused) {
                // Resume the timer
                this.endTime = Date.now() + this.remaining;
                localStorage.setItem('timerEndTime', this.endTime);
                localStorage.removeItem('timerPausedRemaining');
                this.isRunning = true;
                this.isPaused = false;
                this.interval = setInterval(() => this.tick(), 1000);
            }
        },

        stopTimer() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
            this.isRunning = false;
            this.isPaused = false;
            this.remaining = 0;
            this.endTime = null;
            localStorage.removeItem('timerEndTime');
            localStorage.removeItem('timerPausedRemaining');
        },

        tick() {
            if (!this.endTime) return;

            this.remaining = Math.max(0, this.endTime - Date.now());

            if (this.remaining <= 0) {
                this.timerComplete();
            }
        },

        timerComplete() {
            this.stopTimer();
            // Play notification sound or show alert
            if (Notification.permission === 'granted') {
                new Notification('Timer Complete!', { body: 'Your timer has finished.' });
            } else {
                alert('Timer Complete!');
            }
        },

        formatTime(ms) {
            const totalSeconds = Math.ceil(ms / 1000);
            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
    };
}
</script>
