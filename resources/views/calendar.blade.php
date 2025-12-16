<x-app-layout>
    <x-slot name="sidebar">
        <x-sidebar :projects="$projects" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Calendar
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="calendarWidget()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Calendar Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="p-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <button @click="prevMonth()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 min-w-[200px] text-center" x-text="currentMonth"></h3>
                        <button @click="nextMonth()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <button @click="goToToday()" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition text-gray-700 dark:text-gray-200">
                            Today
                        </button>
                    </div>
                    <button @click="openCreateModal(new Date())" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Event
                    </button>
                </div>

                <!-- Calendar Grid -->
                <div class="p-4">
                    <!-- Day Headers -->
                    <div class="grid grid-cols-7 mb-2">
                        <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2" x-text="day"></div>
                        </template>
                    </div>

                    <!-- Calendar Days -->
                    <div class="grid grid-cols-7 gap-1">
                        <template x-for="(day, index) in calendarDays" :key="index">
                            <div
                                @click="openCreateModal(day.date)"
                                class="min-h-[100px] p-1 border border-gray-100 dark:border-gray-700 rounded-lg cursor-pointer transition"
                                :class="{
                                    'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700': day.isCurrentMonth,
                                    'bg-gray-50 dark:bg-gray-900 opacity-50': !day.isCurrentMonth,
                                    'ring-2 ring-blue-500': day.isToday
                                }">
                                <!-- Day Number -->
                                <div class="text-right mb-1">
                                    <span class="text-sm font-medium"
                                          :class="{
                                              'text-gray-900 dark:text-gray-100': day.isCurrentMonth,
                                              'text-gray-400 dark:text-gray-600': !day.isCurrentMonth,
                                              'bg-blue-600 text-white px-2 py-0.5 rounded-full': day.isToday
                                          }"
                                          x-text="day.date.getDate()"></span>
                                </div>

                                <!-- Events -->
                                <div class="space-y-1 overflow-hidden" style="max-height: 60px;">
                                    <template x-for="event in day.events.slice(0, 3)" :key="event.id">
                                        <div
                                            @click.stop="openEditModal(event)"
                                            class="text-xs px-1.5 py-0.5 rounded truncate cursor-pointer hover:opacity-80 transition"
                                            :class="getEventClasses(event)"
                                            x-text="event.title">
                                        </div>
                                    </template>
                                    <div x-show="day.events.length > 3" class="text-xs text-gray-500 dark:text-gray-400 px-1">
                                        +<span x-text="day.events.length - 3"></span> more
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Modal -->
        <div x-show="showEventModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" @click="closeModal()"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6" @click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="editingEvent ? 'Edit Event' : 'New Event'"></h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveEvent()">
                        <!-- Title -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                            <input type="text" x-model="eventForm.title" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea x-model="eventForm.description" rows="2"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <!-- Date/Time Row -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start</label>
                                <input type="date" x-model="eventForm.startDate" required
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 mb-2">
                                <input type="time" x-model="eventForm.startTime" required
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End</label>
                                <input type="date" x-model="eventForm.endDate" required
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 mb-2">
                                <input type="time" x-model="eventForm.endTime" required
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Color -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                            <div class="flex gap-2 flex-wrap">
                                <template x-for="color in colors" :key="color">
                                    <button type="button" @click="eventForm.color = color"
                                            class="w-8 h-8 rounded-full border-2 transition"
                                            :class="{
                                                'border-gray-900 dark:border-white scale-110': eventForm.color === color,
                                                'border-transparent hover:scale-105': eventForm.color !== color,
                                                'bg-blue-500': color === 'blue',
                                                'bg-green-500': color === 'green',
                                                'bg-red-500': color === 'red',
                                                'bg-purple-500': color === 'purple',
                                                'bg-yellow-500': color === 'yellow',
                                                'bg-pink-500': color === 'pink',
                                                'bg-indigo-500': color === 'indigo',
                                                'bg-gray-500': color === 'gray'
                                            }">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Recurrence -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Repeat</label>
                            <select x-model="eventForm.recurrence"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Does not repeat</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="biweekly">Bi-weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Recurrence End Date -->
                        <div class="mb-6" x-show="eventForm.recurrence" x-transition>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Repeat Until</label>
                            <input type="date" x-model="eventForm.recurrenceEndDate"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between">
                            <button type="button" x-show="editingEvent" @click="deleteEvent()"
                                    class="px-4 py-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                Delete
                            </button>
                            <div class="flex gap-3 ml-auto">
                                <button type="button" @click="closeModal()"
                                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition font-medium">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Helper to format date in local YYYY-MM-DD without timezone conversion
        function formatDateLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function calendarWidget() {
            return {
                currentDate: new Date(),
                events: [],
                showEventModal: false,
                editingEvent: null,
                selectedDate: null,
                colors: ['blue', 'green', 'red', 'purple', 'yellow', 'pink', 'indigo', 'gray'],

                eventForm: {
                    title: '',
                    description: '',
                    startDate: '',
                    startTime: '09:00',
                    endDate: '',
                    endTime: '10:00',
                    color: 'blue',
                    recurrence: '',
                    recurrenceEndDate: ''
                },

                get currentMonth() {
                    return this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },

                get calendarDays() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();

                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    const startDay = firstDay.getDay();
                    const totalDays = lastDay.getDate();

                    const days = [];

                    // Previous month's trailing days
                    const prevMonthLastDay = new Date(year, month, 0).getDate();
                    for (let i = startDay - 1; i >= 0; i--) {
                        const date = new Date(year, month - 1, prevMonthLastDay - i);
                        days.push({
                            date: date,
                            isCurrentMonth: false,
                            isToday: false,
                            events: this.getEventsForDate(date)
                        });
                    }

                    // Current month's days
                    for (let day = 1; day <= totalDays; day++) {
                        const date = new Date(year, month, day);
                        days.push({
                            date: date,
                            isCurrentMonth: true,
                            isToday: this.isToday(date),
                            events: this.getEventsForDate(date)
                        });
                    }

                    // Next month's leading days
                    const remaining = 42 - days.length;
                    for (let i = 1; i <= remaining; i++) {
                        const date = new Date(year, month + 1, i);
                        days.push({
                            date: date,
                            isCurrentMonth: false,
                            isToday: false,
                            events: this.getEventsForDate(date)
                        });
                    }

                    return days;
                },

                isToday(date) {
                    const today = new Date();
                    return date.toDateString() === today.toDateString();
                },

                getEventsForDate(date) {
                    const dateStr = formatDateLocal(date);
                    return this.events.filter(event => {
                        const eventStart = formatDateLocal(new Date(event.start));
                        const eventEnd = formatDateLocal(new Date(event.end));
                        return dateStr >= eventStart && dateStr <= eventEnd;
                    });
                },

                getEventClasses(event) {
                    const colorMap = {
                        'blue': 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200',
                        'green': 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200',
                        'red': 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200',
                        'purple': 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200',
                        'yellow': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200',
                        'pink': 'bg-pink-100 text-pink-800 dark:bg-pink-900/50 dark:text-pink-200',
                        'indigo': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200',
                        'gray': 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-200'
                    };
                    return colorMap[event.color] || colorMap['blue'];
                },

                prevMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                    this.fetchEvents();
                },

                nextMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                    this.fetchEvents();
                },

                goToToday() {
                    this.currentDate = new Date();
                    this.fetchEvents();
                },

                async fetchEvents() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();

                    const start = new Date(year, month - 1, 1).toISOString();
                    const end = new Date(year, month + 2, 0).toISOString();

                    try {
                        const response = await fetch(`/events?start=${start}&end=${end}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            this.events = await response.json();
                        }
                    } catch (error) {
                        console.error('Error fetching events:', error);
                    }
                },

                openCreateModal(date) {
                    this.selectedDate = date;
                    this.editingEvent = null;
                    this.resetForm();

                    const dateStr = formatDateLocal(date);
                    this.eventForm.startDate = dateStr;
                    this.eventForm.endDate = dateStr;

                    this.showEventModal = true;
                },

                openEditModal(event) {
                    this.editingEvent = event;
                    this.selectedDate = null;

                    const startDate = new Date(event.start);
                    const endDate = new Date(event.end);

                    this.eventForm.title = event.title;
                    this.eventForm.description = event.description || '';
                    this.eventForm.startDate = formatDateLocal(startDate);
                    this.eventForm.startTime = startDate.toTimeString().slice(0, 5);
                    this.eventForm.endDate = formatDateLocal(endDate);
                    this.eventForm.endTime = endDate.toTimeString().slice(0, 5);
                    this.eventForm.color = event.color;
                    this.eventForm.recurrence = event.recurrence || '';
                    this.eventForm.recurrenceEndDate = '';

                    this.showEventModal = true;
                },

                closeModal() {
                    this.showEventModal = false;
                    this.editingEvent = null;
                    this.selectedDate = null;
                },

                resetForm() {
                    this.eventForm = {
                        title: '',
                        description: '',
                        startDate: formatDateLocal(new Date()),
                        startTime: '09:00',
                        endDate: formatDateLocal(new Date()),
                        endTime: '10:00',
                        color: 'blue',
                        recurrence: '',
                        recurrenceEndDate: ''
                    };
                },

                async saveEvent() {
                    const data = {
                        title: this.eventForm.title,
                        description: this.eventForm.description || null,
                        start_datetime: `${this.eventForm.startDate}T${this.eventForm.startTime}:00`,
                        end_datetime: `${this.eventForm.endDate}T${this.eventForm.endTime}:00`,
                        color: this.eventForm.color,
                        recurrence: this.eventForm.recurrence || null,
                        recurrence_end_date: this.eventForm.recurrenceEndDate || null
                    };

                    const url = this.editingEvent ? `/events/${this.editingEvent.id}` : '/events';
                    const method = this.editingEvent ? 'PUT' : 'POST';

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(data)
                        });

                        if (response.ok) {
                            this.closeModal();
                            this.fetchEvents();
                        } else {
                            const error = await response.json();
                            alert('Error saving event: ' + JSON.stringify(error));
                        }
                    } catch (error) {
                        console.error('Error saving event:', error);
                        alert('Error saving event');
                    }
                },

                async deleteEvent() {
                    if (!this.editingEvent) return;
                    if (!confirm('Are you sure you want to delete this event?')) return;

                    try {
                        const response = await fetch(`/events/${this.editingEvent.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            this.closeModal();
                            this.fetchEvents();
                        }
                    } catch (error) {
                        console.error('Error deleting event:', error);
                    }
                },

                init() {
                    this.fetchEvents();
                }
            };
        }
    </script>
</x-app-layout>
