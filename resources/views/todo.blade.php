<x-app-layout>
    <x-slot name="sidebar">
        <x-sidebar :projects="$projects" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Schedule
        </h2>
    </x-slot>

    <div class="p-4 overflow-hidden" style="height: calc(100vh - 145px);">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-full">
                <div class="p-6 text-gray-900 dark:text-gray-100 h-full">
                    <div id="project-content" class="h-full">

                        <!-- 2-Column Layout -->
                        <div class="flex flex-col lg:flex-row gap-6 h-full">
                            <!-- Left Column: Time Calendar -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg flex-1 flex flex-col min-h-0 overflow-hidden">
                                <div class="p-4 pb-2 flex-shrink-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200" id="schedule-title">Today's Schedule</h4>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button id="prev-day" onclick="navigateDay(-1)" 
                                                    class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    title="Previous day">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                            </button>
                                            <button id="today-btn" onclick="goToToday()" 
                                                    class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-800"
                                                    title="Go to today">
                                                Today
                                            </button>
                                            <button id="next-day" onclick="navigateDay(1)" 
                                                    class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    title="Next day">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Resets weekly on Monday
                                        </div>
                                    </div>
                                </div>
                                <div id="time-calendar" class="flex-1 overflow-y-auto px-4 pb-4">
                                    <!-- Calendar will be populated by JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Right Column: Three Task Categories (Stacked Vertically) -->
                            <div class="flex flex-col gap-4 flex-1 overflow-hidden">
                                <!-- Must Do Tasks -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex-1 flex flex-col min-h-0">
                                    <div class="mb-3">
                                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Must Do</h4>
                                    </div>
                                    
                                    <!-- Add Task Form -->
                                    <div id="add-task-form-must" class="hidden mb-4 p-3 bg-white dark:bg-gray-800 rounded border">
                                        <input type="text" id="new-task-title-must" placeholder="Task title..." 
                                               class="w-full text-sm border border-gray-300 dark:border-gray-500 rounded px-2 py-1 mb-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                                               onkeydown="if(event.key==='Enter') { event.preventDefault(); event.stopPropagation(); addTask('must'); }">
                                        <div class="flex gap-2">
                                            <button onclick="addTask('must')" class="px-3 py-1 text-white hover:underline text-sm">
                                                Add
                                            </button>
                                            <button onclick="hideAddTaskForm('must')" class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Tasks List -->
                                    <div id="tasks-list-must" class="flex-1 overflow-y-auto min-h-0 transition-all duration-150 rounded-lg" ondblclick="showAddTaskFormInline('must', event)">
                                        <!-- Tasks will be populated by JavaScript -->
                                        <div class="text-gray-500 dark:text-gray-400 text-sm text-center py-8 cursor-pointer" ondblclick="showAddTaskFormInline('must', event)">
                                            Double-click to add a task
                                        </div>
                                    </div>
                                </div>

                                <!-- May Do Tasks -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex-1 flex flex-col min-h-0">
                                    <div class="mb-3">
                                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">May Do</h4>
                                    </div>
                                    
                                    <!-- Add Task Form -->
                                    <div id="add-task-form-may" class="hidden mb-4 p-3 bg-white dark:bg-gray-800 rounded border">
                                        <input type="text" id="new-task-title-may" placeholder="Task title..." 
                                               class="w-full text-sm border border-gray-300 dark:border-gray-500 rounded px-2 py-1 mb-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                                               onkeydown="if(event.key==='Enter') { event.preventDefault(); event.stopPropagation(); addTask('may'); }">
                                        <div class="flex gap-2">
                                            <button onclick="addTask('may')" class="px-3 py-1 text-white hover:underline text-sm">
                                                Add
                                            </button>
                                            <button onclick="hideAddTaskForm('may')" class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Tasks List -->
                                    <div id="tasks-list-may" class="flex-1 overflow-y-auto min-h-0 transition-all duration-150 rounded-lg" ondblclick="showAddTaskFormInline('may', event)">
                                        <!-- Tasks will be populated by JavaScript -->
                                        <div class="text-gray-500 dark:text-gray-400 text-sm text-center py-8 cursor-pointer" ondblclick="showAddTaskFormInline('may', event)">
                                            Double-click to add a task
                                        </div>
                                    </div>
                                </div>

                                <!-- Recommended Tasks (from Task Management) -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex-1 flex flex-col min-h-0">
                                    <div class="mb-3">
                                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Recommended</h4>
                                    </div>

                                    <!-- Recommended Subtasks List -->
                                    <div id="recommended-subtasks-list" class="flex-1 overflow-y-auto min-h-0 space-y-2 transition-all duration-150 rounded-lg">
                                        <!-- Subtasks will be populated by JavaScript -->
                                        <div class="text-gray-500 dark:text-gray-400 text-sm text-center py-8">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedDate = new Date(); // Track the currently selected date
        let currentProjectHash = null; // Legacy variable - schedule is now global

        // Helper to format date in local YYYY-MM-DD without timezone conversion
        function formatDateLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Initialize page on load
        document.addEventListener('DOMContentLoaded', function() {
            initializePage();
        });

        async function initializePage() {
            // Update schedule title with day of the week
            updateScheduleTitle();

            // Initialize the time calendar
            initializeTimeCalendar();

            // Load events for the selected date
            loadDailyEvents();

            // Load standalone tasks for Must Do / May Do
            loadStandaloneTasks();

            // Load global recommended subtasks
            loadRecommendedSubtasks();
        }

        // Daily events
        let dailyEvents = [];

        async function loadDailyEvents() {
            const dateStr = formatDateLocal(selectedDate);

            try {
                const response = await fetch(`/events/date?date=${dateStr}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    dailyEvents = await response.json();
                    renderDailyEvents();
                }
            } catch (error) {
                console.error('Error loading daily events:', error);
            }
        }

        function renderDailyEvents() {
            // Remove existing event overlays
            document.querySelectorAll('.event-overlay').forEach(el => el.remove());

            const calendar = document.getElementById('time-calendar');
            if (!calendar) return;

            const timeSlots = calendar.querySelectorAll('.time-slot');
            if (timeSlots.length === 0) return;

            // Make calendar position relative for absolute positioning of overlays
            calendar.style.position = 'relative';

            dailyEvents.forEach(event => {
                const startSlot = event.startSlot;
                const slotCount = event.slotCount;

                if (startSlot >= 40 || slotCount <= 0) return;

                // Find the starting slot element
                const startElement = timeSlots[Math.floor(startSlot)];
                if (!startElement) return;

                // Calculate position and height
                const slotHeight = startElement.offsetHeight;
                const top = startElement.offsetTop;
                const height = slotCount * slotHeight;

                // Create event overlay
                const overlay = document.createElement('div');
                overlay.className = `event-overlay absolute left-12 right-2 rounded-lg p-2 cursor-pointer z-10 border-l-4 ${event.colorClasses.light} ${event.colorClasses.border}`;
                overlay.style.top = `${top}px`;
                overlay.style.height = `${Math.max(height, 24)}px`;
                overlay.style.minHeight = '24px';
                overlay.dataset.eventId = event.id;

                overlay.innerHTML = `
                    <div class="font-semibold text-sm ${event.colorClasses.text} truncate">${escapeHtml(event.title)}</div>
                    <div class="text-xs ${event.colorClasses.text} opacity-75">${event.startTime} - ${event.endTime}</div>
                `;

                overlay.onclick = () => {
                    window.location.href = `/calendar?edit=${event.id}`;
                };

                calendar.appendChild(overlay);
            });
        }

        function initializeTimeCalendar() {
            const calendar = document.getElementById('time-calendar');
            calendar.innerHTML = ''; // Clear existing content

            // Generate 15-minute slots from 8:00 AM to 6:00 PM (40 slots)
            // Calendar is read-only - displays time reference only
            for (let i = 0; i < 40; i++) {
                const hour = 8 + Math.floor(i / 4); // Start at 8 AM
                const minute = (i % 4) * 15; // 0, 15, 30, 45

                const timeSlot = createTimeSlot(hour, minute);
                calendar.appendChild(timeSlot);
            }

            // Scroll to current time if viewing today
            const today = new Date();
            const isViewingToday = selectedDate.toDateString() === today.toDateString();

            if (isViewingToday) {
                setTimeout(() => {
                    scrollToCurrentTime();
                }, 100);
            }
        }

        function scrollToCurrentTime() {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();

            // Round to closest 15-minute slot
            let targetHour = currentHour;
            let targetMinute = Math.floor(currentMinute / 15) * 15;

            // Find the corresponding time slot
            const calendar = document.getElementById('time-calendar');
            const timeSlots = calendar.querySelectorAll('.time-slot');

            for (let slot of timeSlots) {
                const slotHour = parseInt(slot.dataset.hour);
                const slotMinute = parseInt(slot.dataset.minute);

                if (slotHour === targetHour && slotMinute === targetMinute) {
                    // Use scrollTop on the container instead of scrollIntoView
                    // to prevent scrolling parent containers and losing the header
                    const scrollTarget = slot.offsetTop - 20; // Small offset for better visibility
                    calendar.scrollTo({ top: scrollTarget, behavior: 'smooth' });
                    break;
                }
            }
        }

        function createTimeSlot(hour, minute) {
            const isCurrentTime = isCurrentTimeSlot(hour, minute);
            const isOnTheHour = minute === 0;

            const slotDiv = document.createElement('div');
            slotDiv.className = `time-slot border-b border-gray-200 dark:border-gray-600 py-1 px-2 ${isCurrentTime ? 'current-time bg-blue-50 dark:bg-blue-900' : ''} ${isOnTheHour ? 'border-t-2 border-t-gray-300 dark:border-t-gray-500' : ''}`;
            slotDiv.dataset.hour = hour;
            slotDiv.dataset.minute = minute;

            // Only show time label on the hour
            if (isOnTheHour) {
                const timeString = formatTime(hour, minute);
                slotDiv.innerHTML = `
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${timeString}</span>
                    </div>
                `;
            } else {
                // Empty slot for 15/30/45 minute marks
                slotDiv.innerHTML = `<div class="h-4"></div>`;
            }

            return slotDiv;
        }

        function formatTime(hour, minute) {
            const period = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;
            const displayMinute = minute.toString().padStart(2, '0');
            return `${displayHour}:${displayMinute} ${period}`;
        }

        function isCurrentTimeSlot(hour, minute) {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();

            // Check if current time falls within this 15-minute slot
            const slotStart = minute;
            const slotEnd = minute + 15;

            return hour === currentHour && currentMinute >= slotStart && currentMinute < slotEnd;
        }

        function isTimeSlotOverdue(hour, minute, slotDate, isCompleted) {
            if (isCompleted) return false; // Completed tasks are never overdue
            
            const now = new Date();
            const slotDateTime = new Date(slotDate);
            slotDateTime.setHours(hour, minute, 0, 0);
            
            return slotDateTime < now;
        }

        function editTimeSlot(textDisplayElement) {
            const slot = textDisplayElement.closest('.time-slot');
            
            const textInput = slot.querySelector('.text-input');
            const textDisplay = slot.querySelector('.text-display');
            const input = textInput.querySelector('input');
            
            // Get original value from text content (handling both checkbox and plain text)
            const textContent = slot.querySelector('.text-display-content');
            const originalValue = textContent ? textContent.textContent.trim() : textDisplay.textContent.trim();
            input.dataset.originalValue = originalValue;
            
            // Set input value to current display text
            input.value = originalValue;
            
            // Show input, hide display
            textInput.classList.remove('hidden');
            textDisplay.classList.add('hidden');
            
            // Focus input
            input.focus();
            input.select();
        }

        async function saveTimeSlot(input) {
            const slot = input.closest('.time-slot');
            const textInput = slot.querySelector('.text-input');
            const textDisplay = slot.querySelector('.text-display');
            
            const hour = parseInt(slot.dataset.hour);
            const minute = parseInt(slot.dataset.minute);
            const content = input.value.trim();
            
            // Update display text with checkbox structure if content exists
            if (content.trim() !== '') {
                const isOverdue = isTimeSlotOverdue(hour, minute, selectedDate, false);
                const overdueClass = isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400';
                const responseData = await response.json();
                textDisplay.innerHTML = `
                    <div class="flex items-center gap-2">
                        <input type="checkbox" class="task-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                               onchange="toggleTimeSlotCompletion(this)">
                        <span class="text-display-content text-xs ${overdueClass} cursor-pointer flex-1" 
                              ondblclick="editTimeSlot(this.parentElement.parentElement)"
                              draggable="true"
                              data-task-id="${responseData.id}"
                              onmousedown="startScheduledTaskDrag(event, this)">${content}</span>
                        <button class="delete-scheduled-task text-red-500 hover:text-red-700 text-xs ml-1 opacity-75 hover:opacity-100" 
                                onclick="deleteScheduledTask('${responseData.id}', this)"
                                title="Delete task">×</button>
                    </div>
                `;
            } else {
                textDisplay.innerHTML = `<div class="text-display-content text-xs text-gray-600 dark:text-gray-400 min-h-[1rem] cursor-pointer" ondblclick="editTimeSlot(this.parentElement)">${content}</div>`;
            }
            
            // Show display, hide input
            textInput.classList.add('hidden');
            textDisplay.classList.remove('hidden');
            
            // Save to backend
            try {
                const selectedDateString = formatDateLocal(selectedDate);
                const timeString = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');

                let response;

                if (content === '') {
                    // Delete the time slot if content is empty
                    response = await fetch(`/projects/${currentProjectHash}/time-slots/delete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            date: selectedDateString,
                            time: timeString
                        })
                    });
                } else {
                    // Create or update the time slot
                    response = await fetch(`/projects/${currentProjectHash}/time-slots`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            date: selectedDateString,
                            time: timeString,
                            content: content
                        })
                    });
                }
                
                if (!response.ok) {
                    const responseText = await response.text();
                    console.error('Server error response text:', responseText);
                    
                    let errorData;
                    try {
                        errorData = JSON.parse(responseText);
                    } catch (e) {
                        errorData = { message: `HTTP ${response.status}: ${responseText}` };
                    }
                    
                    throw new Error(`Failed to save time slot: ${errorData.message || errorData.error || 'Unknown error'}`);
                }
            } catch (error) {
                console.error('Error saving time slot:', error);
                alert('Error saving time slot: ' + error.message);
                
                // Revert display on error
                textDisplay.textContent = input.dataset.originalValue || '';
            }
        }

        function handleTimeSlotKeydown(event, input) {
            if (event.key === 'Enter') {
                input.blur(); // This will trigger saveTimeSlot
            } else if (event.key === 'Escape') {
                // Cancel editing without saving
                const slot = input.closest('.time-slot');
                const textInput = slot.querySelector('.text-input');
                const textDisplay = slot.querySelector('.text-display');
                
                textInput.classList.add('hidden');
                textDisplay.classList.remove('hidden');
            }
        }

        function updateScheduleTitle() {
            const dayOfWeek = selectedDate.toLocaleDateString('en-US', { weekday: 'long' });
            const currentDate = selectedDate.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });
            
            // Calculate start of current work week (Monday)
            const now = new Date();
            const monday = new Date(now);
            monday.setDate(now.getDate() - (now.getDay() === 0 ? 6 : now.getDay() - 1));
            const mondayDate = monday.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });
            
            // Calculate end of current work week (Friday)
            const friday = new Date(monday);
            friday.setDate(monday.getDate() + 4);
            const fridayDate = friday.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });
            
            // Check if selected date is today
            const today = new Date();
            const isToday = selectedDate.toDateString() === today.toDateString();
            
            document.getElementById('schedule-title').textContent = isToday ? `Today, ${currentDate}` : `${dayOfWeek}, ${currentDate}`;
            
            // Add a subtitle showing the work week
            const existingSubtitle = document.getElementById('schedule-subtitle');
            if (existingSubtitle) {
                existingSubtitle.remove();
            }
            
            const subtitle = document.createElement('p');
            subtitle.id = 'schedule-subtitle';
            subtitle.className = 'text-sm text-gray-600 dark:text-gray-400 mt-1';
            subtitle.textContent = `Work Week: ${mondayDate} - ${fridayDate}`;
            
            const titleElement = document.getElementById('schedule-title');
            titleElement.parentNode.insertBefore(subtitle, titleElement.nextSibling);
            
            updateNavigationButtons();
        }
        
        function navigateDay(direction) {
            const newDate = new Date(selectedDate);
            newDate.setDate(selectedDate.getDate() + direction);
            
            // Check if new date is within current work week
            const now = new Date();
            const monday = new Date(now);
            monday.setDate(now.getDate() - (now.getDay() === 0 ? 6 : now.getDay() - 1));
            monday.setHours(0, 0, 0, 0); // Set to start of day
            
            const friday = new Date(monday);
            friday.setDate(monday.getDate() + 4);
            friday.setHours(23, 59, 59, 999); // Set to end of day
            
            // Normalize new date to compare properly
            const normalizedNewDate = new Date(newDate);
            normalizedNewDate.setHours(12, 0, 0, 0); // Set to middle of day for comparison
            
            // Only allow navigation within current work week (Monday-Friday)
            if (normalizedNewDate >= monday && normalizedNewDate <= friday) {
                selectedDate = newDate;
                initializeTimeCalendar();
                loadDailyEvents();
                updateScheduleTitle();
            }
        }
        
        function goToToday() {
            selectedDate = new Date();
            initializeTimeCalendar();
            loadDailyEvents();
            updateScheduleTitle();
        }
        
        function updateNavigationButtons() {
            const now = new Date();
            const monday = new Date(now);
            monday.setDate(now.getDate() - (now.getDay() === 0 ? 6 : now.getDay() - 1));
            monday.setHours(0, 0, 0, 0);
            
            const friday = new Date(monday);
            friday.setDate(monday.getDate() + 4);
            friday.setHours(23, 59, 59, 999);
            
            const prevDay = new Date(selectedDate);
            prevDay.setDate(selectedDate.getDate() - 1);
            prevDay.setHours(12, 0, 0, 0);
            
            const nextDay = new Date(selectedDate);
            nextDay.setDate(selectedDate.getDate() + 1);
            nextDay.setHours(12, 0, 0, 0);
            
            // Disable buttons if at the boundaries of the work week
            document.getElementById('prev-day').disabled = prevDay < monday;
            document.getElementById('next-day').disabled = nextDay > friday;
            
            // Hide/show today button based on selected date
            const today = new Date();
            const isToday = selectedDate.toDateString() === today.toDateString();
            const todayBtn = document.getElementById('today-btn');
            todayBtn.style.opacity = isToday ? '0.5' : '1';
            todayBtn.style.pointerEvents = isToday ? 'none' : 'auto';
        }

        // Task Management Functions
        async function loadTasks() {
            if (!currentProjectHash) return;

            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const tasks = await response.json();
                    renderTasks(tasks);
                } else {
                    console.error('Failed to load tasks');
                }
            } catch (error) {
                console.error('Error loading tasks:', error);
            }
        }

        async function loadTasksWithCacheBuster() {
            if (!currentProjectHash) return;

            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks?_t=${Date.now()}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0'
                    }
                });

                if (response.ok) {
                    const tasks = await response.json();
                    renderTasks(tasks);
                } else {
                    console.error('Failed to load tasks');
                }
            } catch (error) {
                console.error('Error loading tasks:', error);
            }
        }

        async function refreshScheduledTasksOnly() {
            if (!currentProjectHash) return;

            try {
                const dateString = formatDateLocal(selectedDate);
                const response = await fetch(`/projects/${currentProjectHash}/time-slots?date=${dateString}&_t=${Date.now()}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0'
                    }
                });

                if (response.ok) {
                    const existingSlots = await response.json();
                    
                    // Update only the content of existing time slots without redrawing
                    const timeSlots = document.querySelectorAll('.time-slot');
                    timeSlots.forEach(slotDiv => {
                        const hour = parseInt(slotDiv.dataset.hour);
                        const minute = parseInt(slotDiv.dataset.minute);
                        const timeKey = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                        
                        const existingTask = existingSlots[timeKey];
                        const existingContent = existingTask ? (existingTask.title || existingTask.content || '') : '';
                        const isCompleted = existingTask ? (existingTask.completed || false) : false;
                        
                        const textDisplay = slotDiv.querySelector('.text-display');
                        
                        if (existingContent.trim() !== '') {
                            const isOverdue = isTimeSlotOverdue(hour, minute, selectedDate, isCompleted);
                            const overdueClass = isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400';
                            textDisplay.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" class="task-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                           ${isCompleted ? 'checked' : ''}
                                           onchange="toggleTimeSlotCompletion(this)">
                                    <span class="text-display-content text-xs ${isCompleted ? 'line-through text-gray-400 dark:text-gray-500' : overdueClass} cursor-pointer flex-1" 
                                          ondblclick="editTimeSlot(this.parentElement.parentElement)"
                                          draggable="true"
                                          data-task-id="${existingTask ? existingTask.id : ''}"
                                          onmousedown="startScheduledTaskDrag(event, this)">${existingContent}</span>
                                    <button class="delete-scheduled-task text-red-500 hover:text-red-700 text-xs ml-1 opacity-75 hover:opacity-100" 
                                            onclick="deleteScheduledTask('${existingTask ? existingTask.id : ''}', this)"
                                            title="Delete task">×</button>
                                </div>
                            `;
                        } else {
                            textDisplay.innerHTML = `<div class="text-display-content text-xs text-gray-600 dark:text-gray-400 min-h-[1rem] cursor-pointer" ondblclick="editTimeSlot(this.parentElement)"></div>`;
                        }
                    });
                } else {
                    console.error('Failed to refresh scheduled tasks');
                }
            } catch (error) {
                console.error('Error refreshing scheduled tasks:', error);
            }
        }

        function renderTasks(tasks) {
            // Clear all task lists
            const categories = ['must', 'may', 'recommended'];
            categories.forEach(category => {
                const tasksList = document.getElementById(`tasks-list-${category}`);
                tasksList.innerHTML = '';
            });

            // Group tasks by category
            const tasksByCategory = {
                must: tasks.filter(task => task.category === 'must'),
                may: tasks.filter(task => task.category === 'may'),
                recommended: tasks.filter(task => task.category === 'recommended')
            };

            // Render tasks in each category
            categories.forEach(category => {
                const categoryTasks = tasksByCategory[category];
                const tasksList = document.getElementById(`tasks-list-${category}`);

                if (categoryTasks.length === 0) {
                    tasksList.innerHTML = '<div class="text-gray-500 dark:text-gray-400 text-sm text-center py-8 cursor-pointer" ondblclick="showAddTaskFormInline(\'' + category + '\', event)">Double-click to add a task</div>';
                } else {
                    categoryTasks.forEach(task => {
                        const taskElement = createTaskElement(task);
                        tasksList.appendChild(taskElement);
                    });
                    
                    // Add double-click area at the bottom for adding new tasks
                    const addArea = document.createElement('div');
                    addArea.className = 'text-gray-400 dark:text-gray-500 text-sm text-center py-4 cursor-pointer hover:text-gray-600 dark:hover:text-gray-400';
                    addArea.innerHTML = 'Double-click to add a task';
                    addArea.ondblclick = (e) => showAddTaskFormInline(category, e);
                    tasksList.appendChild(addArea);
                }
            });

            // Setup drop zones
            setupDropZones();
            setupTaskDropZones();
        }

        function setupDropZones() {
            const categories = ['must', 'may', 'recommended'];
            
            categories.forEach(category => {
                const tasksList = document.getElementById(`tasks-list-${category}`);
                tasksList.addEventListener('dragover', handleDragOver);
                tasksList.addEventListener('drop', (e) => handleDrop(e, category));
                tasksList.addEventListener('dragenter', handleDragEnter);
                tasksList.addEventListener('dragleave', handleDragLeave);
            });
        }

        function setupTaskDropZones() {
            const taskElements = document.querySelectorAll('.task-item');
            taskElements.forEach(task => {
                task.addEventListener('dragover', handleTaskDragOver);
                task.addEventListener('drop', handleTaskDrop);
                task.addEventListener('dragenter', handleTaskDragEnter);
                task.addEventListener('dragleave', handleTaskDragLeave);
            });
        }

        function createTaskElement(task) {
            const taskDiv = document.createElement('div');
            taskDiv.className = 'task-item border-b border-gray-200 dark:border-gray-600 py-2 flex items-center gap-2 cursor-move hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors';
            taskDiv.dataset.taskId = task.id;
            taskDiv.dataset.taskCategory = task.category;
            taskDiv.draggable = true;

            taskDiv.innerHTML = `
                <div class="drag-handle text-gray-400 cursor-move mr-1">⋮⋮</div>
                <input type="checkbox" ${task.completed ? 'checked' : ''} 
                       onchange="toggleTaskComplete(${task.id}, this.checked)"
                       class="rounded pointer-events-auto">
                <span class="flex-1 text-sm ${task.completed ? 'line-through text-gray-500 dark:text-gray-400' : 'text-gray-800 dark:text-gray-200'}">${task.title}</span>
                <button class="text-red-500 hover:text-red-700 text-xs pointer-events-auto z-10 relative">
                    ✕
                </button>
            `;

            // Add drag event listeners
            taskDiv.addEventListener('dragstart', handleDragStart);
            taskDiv.addEventListener('dragend', handleDragEnd);
            
            // Ensure delete button clicks work
            const deleteButton = taskDiv.querySelector('button');
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    deleteTask(task.id);
                });
            }

            return taskDiv;
        }

        function showAddTaskFormInline(category, event) {
            event.stopPropagation();
            
            // Check if there's already an input being edited
            if (document.querySelector('.task-input-editing')) {
                return;
            }
            
            const tasksList = document.getElementById(`tasks-list-${category}`);
            
            // Create input element
            const inputContainer = document.createElement('div');
            inputContainer.className = 'task-input-editing border-b border-gray-200 dark:border-gray-600 py-2 flex items-center gap-2';
            inputContainer.innerHTML = `
                <div class="text-gray-400 mr-1">⋮⋮</div>
                <input type="checkbox" disabled class="rounded opacity-50">
                <input type="text" placeholder="Enter task title..." 
                       class="flex-1 text-sm border-none bg-white dark:bg-gray-700 focus:outline-none text-gray-800 dark:text-gray-200 px-2 py-1 rounded"
                       onblur="saveNewTask('${category}', this)" 
                       onkeydown="handleNewTaskKeydown(event, '${category}', this)"
                       autofocus>
                <button onclick="cancelNewTask('${category}')" class="text-gray-400 hover:text-gray-600 text-xs">
                    ✕
                </button>
            `;
            
            // Insert at the beginning of the tasks list
            tasksList.insertBefore(inputContainer, tasksList.firstChild);
            
            // Focus the input
            const input = inputContainer.querySelector('input[type="text"]');
            input.focus();
        }

        function saveNewTask(category, input) {
            const title = input.value.trim();
            if (!title) {
                cancelNewTask(category);
                return;
            }
            
            // Call the existing addTask function
            addTaskFromInput(category, title);
            // Don't call cancelNewTask here - loadTasks() will clean up the editing element
        }

        function cancelNewTask(category) {
            const editingElement = document.querySelector('.task-input-editing');
            if (editingElement && editingElement.parentNode) {
                try {
                    editingElement.remove();
                } catch (error) {
                    // Element was already removed, ignore the error
                }
            }
        }

        function handleNewTaskKeydown(event, category, input) {
            if (event.key === 'Enter') {
                saveNewTask(category, input);
            } else if (event.key === 'Escape') {
                cancelNewTask(category);
            }
        }

        function addTask(category) {
            const debounceKey = `add-task-${category}`;
            
            // Clear any existing timeout for this category
            if (taskCreationTimeouts[debounceKey]) {
                clearTimeout(taskCreationTimeouts[debounceKey]);
            }
            
            // Set a new timeout to execute the actual task creation
            taskCreationTimeouts[debounceKey] = setTimeout(() => {
                executeAddTask(category);
                delete taskCreationTimeouts[debounceKey];
            }, 50); // 50ms debounce
        }

        function executeAddTask(category) {
            const titleInput = document.getElementById(`new-task-title-${category}`);
            const title = titleInput.value.trim();
            
            if (!title) {
                alert('Please enter a task title');
                return;
            }
            
            // Disable the button to prevent clicking during creation
            const button = document.querySelector(`button[onclick="addTask('${category}')"]`);
            if (button) {
                button.disabled = true;
            }
            
            addTaskFromInput(category, title);
            
            // Clear the input and hide the form
            titleInput.value = '';
            hideAddTaskForm(category);
            
            // Re-enable button after a short delay
            if (button) {
                setTimeout(() => {
                    button.disabled = false;
                }, 1000);
            }
        }

        async function addTaskFromInput(category, title) {
            if (!title) {
                alert('Please enter a task title');
                return;
            }
            
            // Prevent duplicate calls within 1 second
            const now = Date.now();
            const key = `${category}-${title}`;
            if (lastTaskCreation[key] && (now - lastTaskCreation[key]) < 1000) {
                return;
            }
            lastTaskCreation[key] = now;
            
            // Clean up old entries after 2 seconds
            setTimeout(() => {
                delete lastTaskCreation[key];
            }, 2000);

            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        title: title,
                        category: category
                    })
                });

                if (response.ok) {
                    const task = await response.json();
                    loadTasks(); // Reload all tasks
                } else {
                    const errors = await response.json();
                    alert('Error creating task: ' + (errors.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error creating task:', error);
                alert('Error creating task. Please try again.');
            }
        }

        async function toggleTaskComplete(taskId, completed) {
            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        completed: completed
                    })
                });

                if (response.ok) {
                    loadTasks(); // Reload to update styling
                } else {
                    console.error('Failed to update task');
                    // Revert checkbox state
                    const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
                    if (checkbox) checkbox.checked = !completed;
                }
            } catch (error) {
                console.error('Error updating task:', error);
                // Revert checkbox state
                const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
                if (checkbox) checkbox.checked = !completed;
            }
        }

        async function deleteTask(taskId) {
            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    loadTasks(); // Reload tasks
                } else {
                    console.error('Failed to delete task');
                    alert('Error deleting task. Please try again.');
                }
            } catch (error) {
                console.error('Error deleting task:', error);
                alert('Error deleting task. Please try again.');
            }
        }

        async function deleteTaskSilent(taskId) {
            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    loadTasks(); // Reload tasks
                } else {
                    console.error('Failed to delete task');
                }
            } catch (error) {
                console.error('Error deleting task:', error);
            }
        }

        function toggleTimeSlotCompletion(checkbox) {
            const textContent = checkbox.nextElementSibling;
            const slot = checkbox.closest('.time-slot');
            const hour = parseInt(slot.dataset.hour);
            const minute = parseInt(slot.dataset.minute);
            
            if (checkbox.checked) {
                // Task completed - remove overdue styling and add completion styling
                textContent.classList.remove('text-red-600', 'dark:text-red-400', 'text-gray-600', 'dark:text-gray-400');
                textContent.classList.add('line-through', 'text-gray-400', 'dark:text-gray-500');
            } else {
                // Task uncompleted - check if it should be overdue
                textContent.classList.remove('line-through', 'text-gray-400', 'dark:text-gray-500');
                const isOverdue = isTimeSlotOverdue(hour, minute, selectedDate, false);
                if (isOverdue) {
                    textContent.classList.add('text-red-600', 'dark:text-red-400');
                    textContent.classList.remove('text-gray-600', 'dark:text-gray-400');
                } else {
                    textContent.classList.add('text-gray-600', 'dark:text-gray-400');
                    textContent.classList.remove('text-red-600', 'dark:text-red-400');
                }
            }
        }

        async function deleteScheduledTask(taskId, buttonElement) {
            if (!taskId) return;

            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    // Clear the time slot content
                    const timeSlot = buttonElement.closest('.time-slot');
                    const textDisplay = timeSlot.querySelector('.text-display');
                    textDisplay.innerHTML = `<div class="text-display-content text-xs text-gray-600 dark:text-gray-400 min-h-[1rem] cursor-pointer" ondblclick="editTimeSlot(this.parentElement)"></div>`;
                } else {
                    console.error('Failed to delete scheduled task');
                    alert('Error deleting task. Please try again.');
                }
            } catch (error) {
                console.error('Error deleting scheduled task:', error);
                alert('Error deleting task. Please try again.');
            }
        }

        // Drag and Drop Functions
        let draggedTask = null;
        let draggedScheduledTask = null;

        function handleDragStart(e) {
            draggedTask = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('text/html', this.outerHTML);
        }

        function startScheduledTaskDrag(event, element) {
            // Prevent drag from interfering with double-click
            if (event.detail === 2) return;
            
            draggedScheduledTask = element;
            draggedTask = null; // Clear regular task drag
            
            element.addEventListener('dragstart', handleScheduledTaskDragStart);
            element.addEventListener('dragend', handleScheduledTaskDragEnd);
        }

        function handleScheduledTaskDragStart(e) {
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.textContent);
        }

        function handleScheduledTaskDragEnd(e) {
            this.style.opacity = '1';
            this.removeEventListener('dragstart', handleScheduledTaskDragStart);
            this.removeEventListener('dragend', handleScheduledTaskDragEnd);
            
            // Clean up any drop zone highlights
            document.querySelectorAll('.bg-blue-100, .dark\\:bg-blue-900\\/20').forEach(el => {
                el.classList.remove('bg-blue-100', 'dark:bg-blue-900/20', 'border-2', 'border-blue-400', 'border-dashed');
            });
            
            draggedScheduledTask = null;
        }

        function handleDragEnd(e) {
            this.style.opacity = '1';
            
            // Clean up any drop zone highlights
            const dropZones = document.querySelectorAll('[id^="tasks-list-"]');
            dropZones.forEach(zone => {
                zone.classList.remove('bg-green-100', 'dark:bg-green-900/20', 'border-2', 'border-green-400', 'border-dashed');
            });
            
            // Clean up time slot highlights
            const timeSlots = document.querySelectorAll('.time-slot');
            timeSlots.forEach(slot => {
                slot.classList.remove('bg-blue-100', 'dark:bg-blue-900/20', 'border-2', 'border-blue-400', 'border-dashed');
            });
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            // Accept both regular tasks and scheduled tasks
            if (draggedTask || draggedScheduledTask) {
                e.dataTransfer.dropEffect = 'move';
            }
            return false;
        }

        function handleDragEnter(e) {
            // Show highlight for both regular tasks and scheduled tasks
            if (draggedTask || draggedScheduledTask) {
                this.classList.add('bg-green-100', 'dark:bg-green-900/20', 'border-2', 'border-green-400', 'border-dashed');
            }
        }

        function handleDragLeave(e) {
            // Only remove highlight if we're actually leaving the drop zone
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('bg-green-100', 'dark:bg-green-900/20', 'border-2', 'border-green-400', 'border-dashed');
            }
        }

        async function handleDrop(e, targetCategory) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            // Handle both regular tasks and scheduled tasks
            if (!draggedTask && !draggedScheduledTask) return false;

            // Handle scheduled task being moved to unscheduled category
            if (draggedScheduledTask) {
                return await handleScheduledTaskDrop(e, targetCategory);
            }

            const taskId = draggedTask.dataset.taskId;
            const currentCategory = draggedTask.dataset.taskCategory;

            // Don't do anything if dropped in same category
            if (currentCategory === targetCategory) {
                return false;
            }

            // Update task category in database
            try {
                const response = await fetch(`/projects/${currentProjectHash}/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        category: targetCategory
                    })
                });

                if (response.ok) {
                    // Reload tasks to reflect the change
                    loadTasks();
                } else {
                    console.error('Failed to update task category');
                    alert('Error moving task. Please try again.');
                }
            } catch (error) {
                console.error('Error updating task category:', error);
                alert('Error moving task. Please try again.');
            }

            return false;
        }

        function handleTaskDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleTaskDragEnter(e) {
            this.style.borderTop = '2px solid #3B82F6';
        }

        function handleTaskDragLeave(e) {
            if (!this.contains(e.relatedTarget)) {
                this.style.borderTop = '';
            }
        }

        async function handleTaskDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            this.style.borderTop = '';

            if (!draggedTask) return false;

            const targetTask = this;
            const targetTaskId = targetTask.dataset.taskId;
            const draggedTaskId = draggedTask.dataset.taskId;
            const targetCategory = targetTask.dataset.taskCategory;
            const draggedCategory = draggedTask.dataset.taskCategory;

            // Don't drop on self
            if (draggedTaskId === targetTaskId) {
                return false;
            }

            // Get the target task's current order
            const targetOrder = parseInt(targetTask.style.order) || 0;

            // If moving within same category, reorder
            if (draggedCategory === targetCategory) {
                // Get all tasks in this category
                const categoryTasks = Array.from(targetTask.parentElement.querySelectorAll('.task-item'))
                    .filter(task => task.dataset.taskCategory === targetCategory);
                
                // Remove dragged task from its current position
                draggedTask.remove();
                
                // Insert before the target task
                targetTask.parentElement.insertBefore(draggedTask, targetTask);
                
                // Update orders in database
                updateTaskOrders(targetCategory);
            } else {
                // Moving between categories - use existing logic
                try {
                    const response = await fetch(`/projects/${currentProjectHash}/tasks/${draggedTaskId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            category: targetCategory
                        })
                    });

                    if (response.ok) {
                        loadTasks();
                    } else {
                        console.error('Failed to update task category');
                        alert('Error moving task. Please try again.');
                    }
                } catch (error) {
                    console.error('Error updating task category:', error);
                    alert('Error moving task. Please try again.');
                }
            }

            return false;
        }

        async function updateTaskOrders(category) {
            const categoryTasks = Array.from(document.querySelectorAll(`#tasks-list-${category} .task-item`));
            const updates = categoryTasks.map((task, index) => ({
                id: task.dataset.taskId,
                order: index + 1
            }));

            // Update each task's order in the database
            for (let update of updates) {
                try {
                    await fetch(`/projects/${currentProjectHash}/tasks/${update.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            order: update.order
                        })
                    });
                } catch (error) {
                    console.error('Error updating task order:', error);
                }
            }
        }

        // Time Slot Drag and Drop Handlers
        function handleTimeSlotDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            this.classList.add('bg-blue-100', 'dark:bg-blue-900/20', 'border-2', 'border-blue-400', 'border-dashed');
            return false;
        }

        function handleTimeSlotDragEnter(e) {
            this.classList.add('bg-blue-100', 'dark:bg-blue-900/20', 'border-2', 'border-blue-400', 'border-dashed');
        }

        function handleTimeSlotDragLeave(e) {
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('bg-blue-100', 'dark:bg-blue-900/20', 'border-2', 'border-blue-400', 'border-dashed');
            }
        }

        async function handleScheduledTaskDrop(e, targetCategory) {
            const taskId = draggedScheduledTask.dataset.taskId;
            if (!taskId) {
                return false;
            }

            // Capture references before making API call (in case drag state gets cleared)
            const timeSlot = draggedScheduledTask.closest('.time-slot');
            const taskTitle = draggedScheduledTask.textContent;

            try {
                // Update task to remove scheduling and set new category
                const response = await fetch(`/projects/${currentProjectHash}/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        category: targetCategory,
                        scheduled_date: null,
                        scheduled_time: null
                    })
                });

                if (response.ok) {
                    // Remove the scheduled task from the time slot
                    const textDisplay = timeSlot.querySelector('.text-display');
                    textDisplay.innerHTML = `<div class="text-display-content text-xs text-gray-600 dark:text-gray-400 min-h-[1rem] cursor-pointer" ondblclick="editTimeSlot(this.parentElement)"></div>`;

                    // Reload both the schedule and tasks to show changes
                    await new Promise(resolve => setTimeout(resolve, 100));
                    await loadTasksWithCacheBuster();

                    // Refresh only the scheduled tasks data without redrawing calendar
                    await refreshScheduledTasksOnly();

                    return true;
                } else {
                    console.error('Failed to update scheduled task');
                    return false;
                }
            } catch (error) {
                console.error('Error updating scheduled task:', error);
                return false;
            }
        }

        async function handleTimeSlotDrop(e) {
            e.preventDefault();
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            // Remove highlight
            this.classList.remove('bg-blue-100', 'dark:bg-blue-900/20', 'border-2', 'border-blue-400', 'border-dashed');

            if (!draggedTask) {
                return false;
            }

            const taskTitle = draggedTask.querySelector('span').textContent;
            const hour = parseInt(this.dataset.hour);
            const minute = parseInt(this.dataset.minute);

            try {
                // Create time slot with task title
                const selectedDateString = formatDateLocal(selectedDate);
                const timeString = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');

                const response = await fetch(`/projects/${currentProjectHash}/time-slots`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        date: selectedDateString,
                        time: timeString,
                        content: taskTitle
                    })
                });
                
                if (response.ok) {
                    // Update the time slot display with checkbox structure
                    const textDisplay = this.querySelector('.text-display');
                    const isOverdue = isTimeSlotOverdue(hour, minute, selectedDate, false);
                    const overdueClass = isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400';
                    
                    const newTask = await response.json();
                    
                    textDisplay.innerHTML = `
                        <div class="flex items-center gap-2">
                            <input type="checkbox" class="task-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   onchange="toggleTimeSlotCompletion(this)">
                            <span class="text-display-content text-xs ${overdueClass} cursor-pointer flex-1" 
                                  ondblclick="editTimeSlot(this.parentElement.parentElement)"
                                  draggable="true"
                                  data-task-id="${newTask.id}"
                                  onmousedown="startScheduledTaskDrag(event, this)">${taskTitle}</span>
                            <button class="delete-scheduled-task text-red-500 hover:text-red-700 text-xs ml-1 opacity-75 hover:opacity-100" 
                                    onclick="deleteScheduledTask('${newTask.id}', this)"
                                    title="Delete task">×</button>
                        </div>
                    `;
                    
                    // Remove the task from the task list
                    const taskId = draggedTask.dataset.taskId;
                    if (taskId) {
                        await deleteTaskSilent(taskId);
                    }
                } else {
                    console.error('Failed to create time slot');
                    alert('Error creating time slot. Please try again.');
                }
            } catch (error) {
                console.error('Error creating time slot:', error);
                alert('Error creating time slot. Please try again.');
            }

            return false;
        }

        async function processIncompleteTasksFromPreviousDays() {
            if (!currentProjectHash) return;

            try {
                // Get today's date
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const todayString = formatDateLocal(today);

                // Get all scheduled tasks for this project
                const response = await fetch(`/projects/${currentProjectHash}/tasks`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) return;

                const allTasks = await response.json();
                
                // Filter for incomplete scheduled tasks from previous days
                const incompletePreviousTasks = allTasks.filter(task => {
                    if (!task.scheduled_date || !task.scheduled_time || task.completed) {
                        return false;
                    }

                    const taskDate = new Date(task.scheduled_date);
                    taskDate.setHours(0, 0, 0, 0);

                    return taskDate < today;
                });

                // Process each incomplete task
                for (const task of incompletePreviousTasks) {
                    await moveIncompleteTaskToToday(task, todayString);
                }

            } catch (error) {
                console.error('Error processing incomplete tasks from previous days:', error);
            }
        }

        async function moveIncompleteTaskToToday(task, todayString) {
            try {
                // Format the original date
                const originalDate = new Date(task.scheduled_date);
                const formattedDate = originalDate.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });

                // Create new title with suffix
                const newTitle = `${task.title} - NOT COMPLETED (${formattedDate})`;

                // Update the task to move to today with new title
                const response = await fetch(`/projects/${currentProjectHash}/tasks/${task.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        title: newTitle,
                        scheduled_date: todayString,
                        scheduled_time: task.scheduled_time, // Keep same time
                        category: 'scheduled',
                        completed: false
                    })
                });

                if (!response.ok) {
                    console.error(`Failed to move task ${task.id} to today`);
                }

            } catch (error) {
                console.error(`Error moving task ${task.id} to today:`, error);
            }
        }

        // Load standalone tasks for Must Do / May Do sections
        async function loadStandaloneTasks() {
            try {
                const response = await fetch('/schedule/tasks', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    renderStandaloneTasks('must', data.must || []);
                    renderStandaloneTasks('may', data.may || []);
                }
            } catch (error) {
                console.error('Error loading standalone tasks:', error);
            }
        }

        function renderStandaloneTasks(category, tasks) {
            const container = document.getElementById(`tasks-list-${category}`);

            // Set up container-level drop handling for easier drops
            container.ondragover = (e) => handleContainerDragOver(e, category);
            container.ondragenter = (e) => handleContainerDragEnter(e, container);
            container.ondragleave = (e) => handleContainerDragLeave(e, container);
            container.ondrop = (e) => handleContainerDrop(e, category, tasks.length);

            if (tasks.length === 0) {
                container.innerHTML = `
                    <div class="text-gray-500 dark:text-gray-400 text-sm text-center py-8 cursor-pointer drop-zone pointer-events-none">
                        Double-click to add a task
                    </div>
                `;
                // Re-enable pointer events on container for drops when empty
                container.classList.add('min-h-[100px]');
                return;
            }

            container.innerHTML = tasks.map((task, index) => `
                <div class="task-item flex items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 mb-2 cursor-move transition-all duration-150 ease-out"
                     draggable="true"
                     data-task-id="${task.id}"
                     data-category="${category}"
                     data-is-project-task="${task.is_project_task ? 'true' : 'false'}"
                     data-order="${index}"
                     ondragstart="handleTaskDragStart(event, ${task.id}, ${task.is_project_task})"
                     ondragend="handleTaskDragEnd(event)"
                     ondragover="handleTaskDragOverItem(event)"
                     ondragenter="handleTaskDragEnterItem(event)"
                     ondragleave="handleTaskDragLeaveItem(event)"
                     ondrop="handleDropOnTask(event, '${category}', ${index})">
                    <div class="drag-handle text-gray-400 cursor-move">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                        </svg>
                    </div>
                    <input type="checkbox"
                           ${task.completed ? 'checked' : ''}
                           onchange="toggleStandaloneTask(${task.id}, this.checked)"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="flex-1 text-sm ${task.completed ? 'line-through text-gray-400' : 'text-gray-800 dark:text-gray-200'}">
                        ${escapeHtml(task.title)}
                        ${task.is_project_task ? '<span class="ml-1 text-xs text-blue-500">(project)</span>' : ''}
                    </span>
                    <button onclick="deleteStandaloneTask(${task.id})" class="text-gray-400 hover:text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `).join('');

            container.classList.remove('min-h-[100px]');
        }

        // Task CRUD for standalone tasks
        async function addTask(category) {
            const input = document.getElementById(`new-task-title-${category}`);
            const title = input.value.trim();
            if (!title) return;

            try {
                const response = await fetch('/schedule/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ title, category })
                });

                if (response.ok) {
                    input.value = '';
                    hideAddTaskForm(category);
                    loadStandaloneTasks();
                }
            } catch (error) {
                console.error('Error adding task:', error);
            }
        }

        async function toggleStandaloneTask(taskId, completed) {
            try {
                await fetch(`/schedule/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ completed })
                });
                loadStandaloneTasks();
            } catch (error) {
                console.error('Error toggling task:', error);
            }
        }

        async function deleteStandaloneTask(taskId) {
            try {
                await fetch(`/schedule/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                loadStandaloneTasks();
            } catch (error) {
                console.error('Error deleting task:', error);
            }
        }

        function showAddTaskFormInline(category, event) {
            if (event) event.stopPropagation();
            const form = document.getElementById(`add-task-form-${category}`);
            form.classList.remove('hidden');
            document.getElementById(`new-task-title-${category}`).focus();
        }

        function hideAddTaskForm(category) {
            const form = document.getElementById(`add-task-form-${category}`);
            form.classList.add('hidden');
        }

        // Drag and drop handling
        let draggedTaskId = null;
        let draggedSubtaskId = null;
        let draggedIsProjectTask = false;
        let currentMustTasks = [];
        let currentMayTasks = [];

        function handleTaskDragStart(event, taskId, isProjectTask) {
            draggedTaskId = taskId;
            draggedSubtaskId = null;
            draggedIsProjectTask = isProjectTask;
            event.target.style.opacity = '0.5';
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', taskId);
        }

        function handleSubtaskDragStart(event, subtaskId) {
            draggedSubtaskId = subtaskId;
            draggedTaskId = null;
            draggedIsProjectTask = true; // Subtasks from recommended are always project tasks
            event.target.style.opacity = '0.5';
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', subtaskId);
        }

        function handleTaskDragEnd(event) {
            event.target.style.opacity = '1';
            draggedTaskId = null;
            draggedSubtaskId = null;
            draggedIsProjectTask = false;
            // Remove all drag-over and highlight styling from everywhere
            document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
            document.querySelectorAll('.ring-2').forEach(el => {
                el.classList.remove('ring-2', 'ring-blue-400', 'ring-green-400', 'ring-offset-1', 'ring-inset', 'bg-blue-50', 'bg-green-50/50', 'dark:bg-blue-900/30', 'dark:bg-green-900/20');
            });
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
        }

        function handleTaskDragOverItem(event) {
            event.preventDefault();
            event.stopPropagation();
            event.dataTransfer.dropEffect = 'move';
        }

        function handleTaskDragEnterItem(event) {
            event.preventDefault();
            event.stopPropagation();
            // Add visual indicator - highlight the item being hovered
            event.currentTarget.classList.add('ring-2', 'ring-blue-400', 'ring-offset-1', 'bg-blue-50', 'dark:bg-blue-900/30');
        }

        function handleTaskDragLeaveItem(event) {
            // Only remove if actually leaving (not entering a child)
            if (!event.currentTarget.contains(event.relatedTarget)) {
                event.currentTarget.classList.remove('ring-2', 'ring-blue-400', 'ring-offset-1', 'bg-blue-50', 'dark:bg-blue-900/30');
            }
        }

        // Container-level drag handlers for easier drops
        function handleContainerDragOver(event, category) {
            // Only accept if we're dragging something
            if (draggedTaskId || draggedSubtaskId) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
            }
        }

        function handleContainerDragEnter(event, container) {
            if (draggedTaskId || draggedSubtaskId) {
                event.preventDefault();
                // Add highlight to the entire container
                container.classList.add('ring-2', 'ring-green-400', 'ring-inset', 'bg-green-50/50', 'dark:bg-green-900/20');
            }
        }

        function handleContainerDragLeave(event, container) {
            // Only remove if actually leaving the container (not entering a child)
            if (!container.contains(event.relatedTarget)) {
                container.classList.remove('ring-2', 'ring-green-400', 'ring-inset', 'bg-green-50/50', 'dark:bg-green-900/20');
            }
        }

        async function handleContainerDrop(event, category, taskCount) {
            event.preventDefault();
            event.stopPropagation();

            // Remove highlight
            const container = event.currentTarget;
            container.classList.remove('ring-2', 'ring-green-400', 'ring-inset', 'bg-green-50/50', 'dark:bg-green-900/20');

            // Handle subtask from Recommended being dropped
            if (draggedSubtaskId) {
                await moveSubtaskToCategory(draggedSubtaskId, category, taskCount);
                draggedSubtaskId = null;
                draggedIsProjectTask = false;
                return;
            }

            // Handle task being dropped (reorder/move between categories)
            if (draggedTaskId) {
                await reorderTask(draggedTaskId, category, taskCount);
                draggedTaskId = null;
                draggedIsProjectTask = false;
                return;
            }
        }

        async function handleDropOnTask(event, category, targetIndex) {
            event.preventDefault();
            event.stopPropagation();
            // Remove all visual feedback
            event.currentTarget.classList.remove('drag-over', 'ring-2', 'ring-blue-400', 'ring-offset-1', 'bg-blue-50', 'dark:bg-blue-900/30');

            // Determine what was dropped and handle appropriately
            if (draggedSubtaskId) {
                // Moving from Recommended to Must/May at specific position
                await moveSubtaskToCategory(draggedSubtaskId, category, targetIndex);
            } else if (draggedTaskId) {
                // Reordering within or between Must/May
                await reorderTask(draggedTaskId, category, targetIndex);
            }

            draggedTaskId = null;
            draggedSubtaskId = null;
            draggedIsProjectTask = false;
        }

        async function handleDropToCategory(event, category, targetIndex) {
            event.preventDefault();
            event.currentTarget.classList.remove('drag-over');

            if (draggedSubtaskId) {
                // Move subtask from Recommended to Must/May
                await moveSubtaskToCategory(draggedSubtaskId, category, targetIndex);
            } else if (draggedTaskId) {
                // Move task between categories or reorder
                await reorderTask(draggedTaskId, category, targetIndex);
            }

            draggedTaskId = null;
            draggedSubtaskId = null;
            draggedIsProjectTask = false;
        }

        async function moveSubtaskToCategory(subtaskId, category, targetIndex) {
            try {
                const response = await fetch(`/schedule/subtasks/${subtaskId}/move`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ category })
                });

                if (response.ok) {
                    const data = await response.json();
                    loadStandaloneTasks();
                    renderRecommendedSubtasks(data.recommended);
                }
            } catch (error) {
                console.error('Error moving subtask:', error);
            }
        }

        async function reorderTask(taskId, newCategory, targetIndex) {
            // Get current tasks to build the new order
            const response = await fetch('/schedule/tasks', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) return;

            const data = await response.json();
            let mustTasks = [...(data.must || [])];
            let mayTasks = [...(data.may || [])];

            // Find and remove the dragged task from its current position
            const draggedFromMust = mustTasks.findIndex(t => t.id === taskId);
            const draggedFromMay = mayTasks.findIndex(t => t.id === taskId);

            let draggedTask = null;
            if (draggedFromMust !== -1) {
                draggedTask = mustTasks.splice(draggedFromMust, 1)[0];
            } else if (draggedFromMay !== -1) {
                draggedTask = mayTasks.splice(draggedFromMay, 1)[0];
            }

            if (!draggedTask) return;

            // Insert at new position
            if (newCategory === 'must') {
                mustTasks.splice(targetIndex, 0, { ...draggedTask, category: 'must' });
            } else {
                mayTasks.splice(targetIndex, 0, { ...draggedTask, category: 'may' });
            }

            // Build reorder payload
            const tasks = [
                ...mustTasks.map((t, i) => ({ id: t.id, order: i, category: 'must' })),
                ...mayTasks.map((t, i) => ({ id: t.id, order: i, category: 'may' }))
            ];

            try {
                await fetch('/schedule/tasks/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ tasks })
                });

                loadStandaloneTasks();
            } catch (error) {
                console.error('Error reordering tasks:', error);
            }
        }

        // Load recommended subtasks from all projects
        async function loadRecommendedSubtasks() {
            const container = document.getElementById('recommended-subtasks-list');

            try {
                const response = await fetch('/schedule/recommended', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const subtasks = await response.json();
                    renderRecommendedSubtasks(subtasks);
                } else {
                    container.innerHTML = '<div class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">Failed to load tasks</div>';
                }
            } catch (error) {
                console.error('Error loading recommended subtasks:', error);
                container.innerHTML = '<div class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">Error loading tasks</div>';
            }
        }

        function renderRecommendedSubtasks(subtasks) {
            const container = document.getElementById('recommended-subtasks-list');

            if (subtasks.length === 0) {
                container.innerHTML = `
                    <div class="text-gray-500 dark:text-gray-400 text-sm text-center py-8">
                        <p>No recommended tasks</p>
                        <p class="text-xs mt-2">Add subtasks to your projects to see recommendations</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = subtasks.map(subtask => {
                const priorityColors = {
                    high: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    med: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    low: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                };

                const dueDate = subtask.due_date ? new Date(subtask.due_date) : null;
                const isOverdue = dueDate && dueDate < new Date();
                const dueDateStr = dueDate ? dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '';
                const parentName = subtask.parent ? subtask.parent.title : '';
                const projectName = subtask.project ? subtask.project.name : '';

                return `
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 cursor-move transition-all duration-150 ease-out hover:shadow-md"
                         draggable="true"
                         data-subtask-id="${subtask.id}"
                         ondragstart="handleSubtaskDragStart(event, ${subtask.id})"
                         ondragend="handleTaskDragEnd(event)">
                        <div class="flex items-start gap-3">
                            <input type="checkbox"
                                   onchange="completeRecommendedSubtask(${subtask.id}, this.checked)"
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${escapeHtml(subtask.title)}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    ${projectName ? escapeHtml(projectName) : ''}${parentName && projectName ? ' / ' : ''}${parentName ? escapeHtml(parentName) : ''}
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full ${priorityColors[subtask.priority]}">
                                    ${subtask.priority.charAt(0).toUpperCase() + subtask.priority.slice(1)}
                                </span>
                                ${dueDateStr ? `<span class="text-xs ${isOverdue ? 'text-red-500 font-medium' : 'text-gray-500 dark:text-gray-400'}">${dueDateStr}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function completeRecommendedSubtask(subtaskId, completed) {
            if (!completed) return; // Only handle completion, not unchecking

            try {
                const response = await fetch(`/schedule/subtasks/${subtaskId}/complete`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    // Render the updated recommended list (auto-rehydrated)
                    renderRecommendedSubtasks(data.recommended);
                } else {
                    // Reload on error
                    loadRecommendedSubtasks();
                }
            } catch (error) {
                console.error('Error completing subtask:', error);
                loadRecommendedSubtasks();
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</x-app-layout>
