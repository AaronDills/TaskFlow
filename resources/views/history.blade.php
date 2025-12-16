<x-app-layout>
    <x-slot name="sidebar">
        <x-sidebar :projects="$projects" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                History
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="historyWidget()" x-init="loadWeekData()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Week Navigation and Metrics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <button @click="prevWeek()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 min-w-[200px] text-center" x-text="weekLabel"></h3>
                            <button @click="nextWeek()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" :disabled="isCurrentWeek" :class="{ 'opacity-50 cursor-not-allowed': isCurrentWeek }">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <button @click="goToCurrentWeek()" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition text-gray-700 dark:text-gray-200">
                                This Week
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Metrics Cards -->
                <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400" x-text="metrics.totalCompleted">0</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">Completed</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400" x-text="metrics.totalCreated">0</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">Created</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400" x-text="metrics.averagePerDay">0</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">Avg/Day</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400" x-text="metrics.streak">0</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">Day Streak</div>
                    </div>
                </div>
            </div>

            <!-- Week View Grid -->
            <div class="grid grid-cols-7 gap-3">
                <template x-for="(day, dateStr) in tasksByDay" :key="dateStr">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
                         :class="{ 'ring-2 ring-blue-500': day.isToday }">
                        <!-- Day Header -->
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700 text-center"
                             :class="day.isToday ? 'bg-blue-50 dark:bg-blue-900/30' : 'bg-gray-50 dark:bg-gray-700'">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="day.dayShort"></div>
                            <div class="text-lg font-bold" :class="day.isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100'" x-text="day.dayNumber"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="day.month"></div>
                        </div>

                        <!-- Task Count Badge -->
                        <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                      :class="day.completedCount > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'">
                                    <span x-text="day.completedCount"></span>
                                    <span class="ml-1">done</span>
                                </span>
                            </div>
                        </div>

                        <!-- Tasks List -->
                        <div class="p-2 max-h-64 overflow-y-auto">
                            <template x-if="day.tasks.length === 0">
                                <div class="text-center text-gray-400 dark:text-gray-500 text-xs py-4">
                                    No tasks
                                </div>
                            </template>
                            <template x-for="task in day.tasks" :key="task.id">
                                <div class="mb-2 p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs">
                                    <div class="font-medium text-gray-800 dark:text-gray-200 truncate" x-text="task.title"></div>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-gray-500 dark:text-gray-400" x-text="task.completed_at"></span>
                                        <template x-if="task.project_name">
                                            <a :href="'/projects/' + task.project_hash + '/manage-tasks'"
                                               class="text-blue-600 dark:text-blue-400 hover:underline truncate max-w-[60px]"
                                               x-text="task.project_name"
                                               @click.stop></a>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function historyWidget() {
            return {
                weekStart: null,
                weekLabel: 'Loading...',
                tasksByDay: {},
                metrics: {
                    totalCompleted: 0,
                    totalCreated: 0,
                    averagePerDay: 0,
                    streak: 0
                },
                loading: false,
                isCurrentWeek: true,

                init() {
                    // Start with current week (Monday)
                    const today = new Date();
                    const dayOfWeek = today.getDay();
                    const diff = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Adjust so Monday is start
                    this.weekStart = new Date(today);
                    this.weekStart.setDate(today.getDate() - diff);
                    this.weekStart.setHours(0, 0, 0, 0);
                },

                async loadWeekData() {
                    this.loading = true;

                    try {
                        const dateStr = this.formatDate(this.weekStart);
                        const response = await fetch(`/history/week?week_start=${dateStr}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.weekLabel = data.weekLabel;
                            this.tasksByDay = data.tasksByDay;
                            this.metrics = data.metrics;
                            this.checkIfCurrentWeek();
                        }
                    } catch (error) {
                        console.error('Error loading week data:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                prevWeek() {
                    this.weekStart.setDate(this.weekStart.getDate() - 7);
                    this.loadWeekData();
                },

                nextWeek() {
                    if (!this.isCurrentWeek) {
                        this.weekStart.setDate(this.weekStart.getDate() + 7);
                        this.loadWeekData();
                    }
                },

                goToCurrentWeek() {
                    const today = new Date();
                    const dayOfWeek = today.getDay();
                    const diff = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
                    this.weekStart = new Date(today);
                    this.weekStart.setDate(today.getDate() - diff);
                    this.weekStart.setHours(0, 0, 0, 0);
                    this.loadWeekData();
                },

                checkIfCurrentWeek() {
                    const today = new Date();
                    const dayOfWeek = today.getDay();
                    const diff = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
                    const currentWeekStart = new Date(today);
                    currentWeekStart.setDate(today.getDate() - diff);
                    currentWeekStart.setHours(0, 0, 0, 0);

                    this.isCurrentWeek = this.weekStart.getTime() >= currentWeekStart.getTime();
                },

                formatDate(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }
            };
        }
    </script>
</x-app-layout>
