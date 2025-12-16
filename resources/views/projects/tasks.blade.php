<x-app-layout>
    <x-slot name="sidebar">
        <x-sidebar :projects="$projects" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" id="project-title">
                    {{ $project->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="taskManager()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-6">
                <!-- Main Content -->
                <div class="flex-1 min-w-0 space-y-8">
                    <!-- ===== TASKS SECTION ===== -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tasks</h3>

                        <!-- Add New Task Button -->
                        <div class="mb-6">
                            <button @click="showNewTaskForm = true"
                                    x-show="!showNewTaskForm"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Task
                            </button>

                            <!-- New Task Form -->
                            <div x-show="showNewTaskForm" x-transition class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                                <form @submit.prevent="createParentTask">
                                    <div class="flex gap-2 items-end">
                                        <div class="flex-1">
                                            <input type="text"
                                                   x-model="newTaskTitle"
                                                   x-ref="newTaskInput"
                                                   placeholder="Task name..."
                                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div class="w-40">
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Deadline (optional)</label>
                                            <input type="date"
                                                   x-model="newTaskDeadline"
                                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </div>
                                        <button type="submit"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 text-sm font-medium">
                                            Create
                                        </button>
                                        <button type="button"
                                                @click="showNewTaskForm = false; newTaskTitle = ''; newTaskDeadline = ''"
                                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 text-sm font-medium">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Task List -->
                        <div class="space-y-4">
                            @forelse($parentTasks as $task)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden {{ $task->on_hold ? 'opacity-60' : '' }}"
                                     x-data="{ expanded: {{ $task->on_hold ? 'false' : 'true' }}, editing: false, editTitle: '{{ $task->title }}', editDeadline: '{{ $task->deadline?->format('Y-m-d') ?? '' }}', onHold: {{ $task->on_hold ? 'true' : 'false' }} }">
                                    <!-- Task Header -->
                                    <div class="p-4 flex items-center gap-3 border-b border-gray-200 dark:border-gray-700"
                                         :class="{ 'bg-green-50 dark:bg-green-900/20': {{ $task->completed ? 'true' : 'false' }}, 'bg-gray-100 dark:bg-gray-700/50': onHold && !{{ $task->completed ? 'true' : 'false' }} }">
                                        <!-- Expand/Collapse -->
                                        <button @click="expanded = !expanded" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-90': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>

                                        <!-- Task Title -->
                                        <div class="flex-1" x-show="!editing">
                                            <span class="font-medium text-gray-900 dark:text-gray-100"
                                                  :class="{ 'line-through text-gray-500': {{ $task->completed ? 'true' : 'false' }} }">
                                                {{ $task->title }}
                                            </span>
                                            @if($task->deadline)
                                                <span class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $task->deadline->isPast() ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                                    Due {{ $task->deadline->format('M j') }}
                                                </span>
                                            @endif
                                            <span x-show="onHold" class="ml-2 text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                On Hold
                                            </span>
                                        </div>

                                        <!-- Edit Form -->
                                        <div class="flex-1 flex gap-2" x-show="editing" x-cloak>
                                            <input type="text"
                                                   x-model="editTitle"
                                                   @keydown.escape="editing = false"
                                                   class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                            <input type="date"
                                                   x-model="editDeadline"
                                                   class="w-36 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                            <button @click="updateParentTask({{ $task->id }}, editTitle, editDeadline); editing = false"
                                                    class="px-2 py-1 bg-blue-600 text-white rounded text-xs">Save</button>
                                        </div>

                                        <!-- Progress -->
                                        @php
                                            $progress = $task->getCompletionProgress();
                                        @endphp
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $progress['completed'] }}/{{ $progress['total'] }}
                                        </span>

                                        <!-- Actions -->
                                        <button @click="toggleOnHold({{ $task->id }})"
                                                class="text-sm px-2 py-1 rounded"
                                                :class="onHold ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 hover:bg-yellow-200' : 'text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20'"
                                                :title="onHold ? 'Resume task' : 'Put on hold'">
                                            <span x-show="!onHold">Hold</span>
                                            <span x-show="onHold">Resume</span>
                                        </button>
                                        <button @click="editing = !editing; editTitle = '{{ $task->title }}'"
                                                class="text-gray-400 hover:text-blue-500 text-sm">
                                            <span x-show="!editing">Edit</span>
                                            <span x-show="editing">Cancel</span>
                                        </button>
                                        <button @click="if(confirm('Delete this task and all subtasks?')) deleteParentTask({{ $task->id }})"
                                                class="text-gray-400 hover:text-red-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Subtasks -->
                                    <div x-show="expanded" x-collapse>
                                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                            @foreach($task->subtasks as $subtask)
                                                <div class="p-4 pl-12 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                                     x-data="{ editingSubtask: false }">
                                                    <!-- Checkbox -->
                                                    <input type="checkbox"
                                                           {{ $subtask->completed ? 'checked' : '' }}
                                                           @change="toggleSubtaskComplete({{ $subtask->id }}, $event.target.checked)"
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                                    <!-- Subtask Info -->
                                                    <div class="flex-1 min-w-0" x-show="!editingSubtask">
                                                        <span class="text-gray-800 dark:text-gray-200"
                                                              :class="{ 'line-through text-gray-400': {{ $subtask->completed ? 'true' : 'false' }} }">
                                                            {{ $subtask->title }}
                                                        </span>
                                                    </div>

                                                    <!-- Edit Subtask Form -->
                                                    <div class="flex-1" x-show="editingSubtask" x-cloak
                                                         x-data="{ title: '{{ $subtask->title }}', priority: '{{ $subtask->priority }}', due_date: '{{ $subtask->due_date?->format('Y-m-d') ?? '' }}' }">
                                                        <div class="flex gap-2 items-center">
                                                            <input type="text" x-model="title"
                                                                   class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                                            <select x-model="priority"
                                                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                                                <option value="high">High</option>
                                                                <option value="med">Med</option>
                                                                <option value="low">Low</option>
                                                            </select>
                                                            <input type="date" x-model="due_date"
                                                                   class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                                            <button @click="updateSubtask({{ $subtask->id }}, title, priority, due_date); editingSubtask = false"
                                                                    class="px-2 py-1 bg-blue-600 text-white rounded text-xs">Save</button>
                                                            <button @click="editingSubtask = false"
                                                                    class="px-2 py-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded text-xs">Cancel</button>
                                                        </div>
                                                    </div>

                                                    <!-- Priority Badge -->
                                                    <span x-show="!editingSubtask"
                                                          class="px-2 py-1 text-xs font-medium rounded-full
                                                                 {{ $subtask->priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                                 {{ $subtask->priority === 'med' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                                 {{ $subtask->priority === 'low' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}">
                                                        {{ ucfirst($subtask->priority) }}
                                                    </span>

                                                    <!-- Due Date -->
                                                    @if($subtask->due_date)
                                                        <span x-show="!editingSubtask"
                                                              class="text-sm {{ $subtask->due_date->isPast() ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                                                            {{ $subtask->due_date->format('M j') }}
                                                        </span>
                                                    @endif

                                                    <!-- Subtask Actions -->
                                                    <button x-show="!editingSubtask" @click="editingSubtask = true"
                                                            class="text-gray-400 hover:text-blue-500 text-xs">
                                                        Edit
                                                    </button>
                                                    <button x-show="!editingSubtask"
                                                            @click="if(confirm('Delete this subtask?')) deleteSubtask({{ $subtask->id }})"
                                                            class="text-gray-400 hover:text-red-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach

                                            <!-- Add Subtask -->
                                            <div class="p-4 pl-12"
                                                 x-data="{ adding: false, title: '', priority: 'med', due_date: '{{ $task->deadline?->format('Y-m-d') ?? '' }}' }">
                                                <button x-show="!adding" @click="adding = true"
                                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                    + Add Subtask
                                                </button>

                                                <div x-show="adding" x-transition class="flex gap-2 items-center">
                                                    <input type="text"
                                                           x-model="title"
                                                           placeholder="Subtask name..."
                                                           class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                                    <select x-model="priority"
                                                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                                        <option value="high">High</option>
                                                        <option value="med">Med</option>
                                                        <option value="low">Low</option>
                                                    </select>
                                                    <input type="date"
                                                           x-model="due_date"
                                                           {{ $task->deadline ? 'disabled' : '' }}
                                                           class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm {{ $task->deadline ? 'cursor-not-allowed' : '' }}"
                                                           {{ $task->deadline ? 'title=Inherited from parent deadline' : '' }}>
                                                    <button @click="createSubtask({{ $task->id }}, title, priority, due_date); adding = false; title = ''; priority = 'med'; due_date = '{{ $task->deadline?->format('Y-m-d') ?? '' }}'"
                                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm">Add</button>
                                                    <button @click="adding = false; title = ''; due_date = '{{ $task->deadline?->format('Y-m-d') ?? '' }}'"
                                                            class="px-3 py-1.5 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-sm">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No tasks</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new task.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- ===== PROJECT DETAILS SECTION ===== -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Details</h3>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Info Card -->
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Information</h4>
                                        <button onclick="toggleEditMode()" id="edit-btn"
                                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            Edit
                                        </button>
                                    </div>

                                    <!-- View Mode -->
                                    <div id="view-mode">
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $project->name }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $project->description ?: 'No description' }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created</label>
                                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $project->created_at->format('M j, Y \a\t g:i A') }}</p>
                                        </div>
                                    </div>

                                    <!-- Edit Mode -->
                                    <form id="edit-mode" class="hidden" onsubmit="updateProject(event)">
                                        <div class="mb-4">
                                            <x-input-label for="edit-name" :value="__('Name')" />
                                            <x-text-input id="edit-name" name="name" type="text" class="mt-1 block w-full"
                                                          value="{{ $project->name }}" required />
                                            <div id="edit-name-error" class="mt-2 text-sm text-red-600 dark:text-red-400"></div>
                                        </div>
                                        <div class="mb-4">
                                            <x-input-label for="edit-description" :value="__('Description')" />
                                            <textarea id="edit-description" name="description" rows="3"
                                                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ $project->description }}</textarea>
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <x-secondary-button type="button" onclick="toggleEditMode()">
                                                Cancel
                                            </x-secondary-button>
                                            <x-primary-button type="submit">
                                                Save Changes
                                            </x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Label Section -->
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Label</h4>
                                    </div>

                                    <div id="label-display">
                                        @if($project->label)
                                            <div class="flex items-center justify-between">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $project->label->bg_class }} {{ $project->label->text_class }}">
                                                    {{ $project->label->name }}
                                                </span>
                                                <button onclick="removeLabel()" class="p-1 text-gray-400 hover:text-red-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">No label assigned</p>
                                        @endif
                                    </div>

                                    <div class="mt-4 space-y-3">
                                        @if($labels->isNotEmpty())
                                            <div>
                                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Select a label</label>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($labels as $label)
                                                        <button onclick="assignLabel({{ $label->id }})"
                                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $label->bg_class }} {{ $label->text_class }} hover:ring-2 hover:ring-offset-2 hover:ring-{{ $label->color }}-400 dark:hover:ring-offset-gray-800 transition-all {{ $project->label_id === $label->id ? 'ring-2 ring-offset-2 ring-' . $label->color . '-400 dark:ring-offset-gray-800' : '' }}">
                                                            {{ $label->name }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <button onclick="openCreateLabelModal()"
                                                class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create new label
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-red-200 dark:border-red-800">
                            <div class="p-6">
                                <h4 class="text-md font-medium text-red-600 dark:text-red-400 mb-4">Danger Zone</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Deleting this project will permanently remove all associated tasks. This action cannot be undone.
                                </p>
                                <button onclick="deleteProject()"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="w-64 flex-shrink-0 space-y-6">
                    <!-- Project Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sticky top-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Status</h3>

                        <div class="space-y-2">
                            @foreach(\App\Models\Project::STATUSES as $value => $label)
                                @if($value !== 'done')
                                    <button @click="updateProjectStatus('{{ $value }}')"
                                            class="w-full text-left px-3 py-2 rounded-md text-sm font-medium transition-colors
                                                   {{ $project->status === $value
                                                       ? ($value === 'ready_to_begin' ? 'bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-gray-100 ring-2 ring-gray-400'
                                                          : ($value === 'in_progress' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 ring-2 ring-blue-400'
                                                             : 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200 ring-2 ring-yellow-400'))
                                                       : 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                                        <div class="flex items-center gap-2">
                                            @if($value === 'ready_to_begin')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($value === 'in_progress')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            @elseif($value === 'on_hold')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                            {{ $label }}
                                        </div>
                                    </button>
                                @endif
                            @endforeach
                        </div>

                        @if($project->status === 'done')
                            <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/30 rounded-md">
                                <div class="flex items-center gap-2 text-green-800 dark:text-green-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">Project Complete!</span>
                                </div>
                            </div>
                        @endif

                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Status will automatically change to "Done" when all subtasks are completed.
                            </p>
                        </div>
                    </div>

                    <!-- Task Statistics -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Task Statistics</h3>

                            <div class="space-y-4">
                                <!-- Total Tasks -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Tasks</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</span>
                                </div>

                                <!-- Completed -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $stats['completed'] }}</span>
                                </div>

                                @if($stats['total'] > 0)
                                    <!-- Progress Bar -->
                                    <div>
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                                 style="width: {{ $stats['total'] > 0 ? ($stats['completed'] / $stats['total']) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- By Category -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">By Category</h3>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Must Do</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['must'] }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">May Do</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['may'] }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Recommended</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['recommended'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Label Modal -->
    <x-modal name="create-label" :show="false" max-width="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Create New Label
            </h2>

            <form id="create-label-form" onsubmit="createAndAssignLabel(event)">
                <div class="mb-4">
                    <x-input-label for="new-label-name" :value="__('Label Name')" />
                    <x-text-input id="new-label-name" name="name" type="text" class="mt-1 block w-full" required maxlength="50" />
                    <div id="new-label-error" class="mt-2 text-sm text-red-600 dark:text-red-400"></div>
                </div>

                <div class="mb-6">
                    <x-input-label :value="__('Color')" />
                    <div class="mt-2 flex flex-wrap gap-3">
                        @php
                            $colors = [
                                'blue' => 'bg-blue-500',
                                'green' => 'bg-green-500',
                                'red' => 'bg-red-500',
                                'yellow' => 'bg-yellow-500',
                                'purple' => 'bg-purple-500',
                                'pink' => 'bg-pink-500',
                                'indigo' => 'bg-indigo-500',
                                'gray' => 'bg-gray-500',
                            ];
                        @endphp
                        @foreach($colors as $color => $bgClass)
                            <label class="cursor-pointer">
                                <input type="radio" name="new-label-color" value="{{ $color }}" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                <div class="w-8 h-8 rounded-full {{ $bgClass }} peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-{{ $color }}-400 dark:peer-checked:ring-offset-gray-800 transition-all hover:scale-110"></div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <x-secondary-button onclick="closeCreateLabelModal()" type="button">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Create & Assign
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Confetti library for celebration animation -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

    <script>
        const projectHash = '{{ $project->hash }}';

        function triggerConfetti() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { x: 0.1, y: 0.6 }
            });

            confetti({
                particleCount: 100,
                spread: 70,
                origin: { x: 0.9, y: 0.6 }
            });

            setTimeout(() => {
                confetti({
                    particleCount: 150,
                    spread: 100,
                    origin: { x: 0.5, y: 0.5 }
                });
            }, 200);
        }

        // Project details functions
        function toggleEditMode() {
            const viewMode = document.getElementById('view-mode');
            const editMode = document.getElementById('edit-mode');
            const editBtn = document.getElementById('edit-btn');

            if (viewMode.classList.contains('hidden')) {
                viewMode.classList.remove('hidden');
                editMode.classList.add('hidden');
                editBtn.textContent = 'Edit';
            } else {
                viewMode.classList.add('hidden');
                editMode.classList.remove('hidden');
                editBtn.textContent = 'Cancel';
            }
        }

        async function updateProject(event) {
            event.preventDefault();

            const name = document.getElementById('edit-name').value;
            const description = document.getElementById('edit-description').value;
            const errorDiv = document.getElementById('edit-name-error');

            errorDiv.textContent = '';

            try {
                const response = await fetch(`/projects/${projectHash}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, description })
                });

                if (!response.ok) {
                    const data = await response.json();
                    if (data.errors?.name) {
                        errorDiv.textContent = data.errors.name[0];
                    } else {
                        errorDiv.textContent = 'Failed to update project.';
                    }
                    return;
                }

                window.location.reload();
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
            }
        }

        async function deleteProject() {
            if (!confirm('Are you sure you want to delete this project? This will permanently delete all tasks and cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/projects/${projectHash}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.href = '{{ route('projects.index') }}';
                } else {
                    alert('Failed to delete project.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        // Label functions
        async function assignLabel(labelId) {
            try {
                const response = await fetch(`/projects/${projectHash}/label`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ label_id: labelId })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to assign label.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        async function removeLabel() {
            try {
                const response = await fetch(`/projects/${projectHash}/label`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ label_id: null })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to remove label.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        function openCreateLabelModal() {
            document.getElementById('new-label-name').value = '';
            document.querySelector('input[name="new-label-color"][value="blue"]').checked = true;
            document.getElementById('new-label-error').textContent = '';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-label' }));
        }

        function closeCreateLabelModal() {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-label' }));
        }

        async function createAndAssignLabel(event) {
            event.preventDefault();

            const name = document.getElementById('new-label-name').value;
            const color = document.querySelector('input[name="new-label-color"]:checked').value;
            const errorDiv = document.getElementById('new-label-error');

            errorDiv.textContent = '';

            try {
                const createResponse = await fetch('/labels', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, color })
                });

                if (!createResponse.ok) {
                    const data = await createResponse.json();
                    if (data.errors?.name) {
                        errorDiv.textContent = data.errors.name[0];
                    } else {
                        errorDiv.textContent = 'Failed to create label.';
                    }
                    return;
                }

                const label = await createResponse.json();

                const assignResponse = await fetch(`/projects/${projectHash}/label`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ label_id: label.id })
                });

                if (assignResponse.ok) {
                    window.location.reload();
                } else {
                    alert('Label created but failed to assign. Please try again.');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
            }
        }

        // Task manager Alpine component
        function taskManager() {
            return {
                showNewTaskForm: false,
                newTaskTitle: '',
                newTaskDeadline: '',
                projectHash: '{{ $project->hash }}',

                async updateProjectStatus(status) {
                    try {
                        const response = await fetch(`/projects/${this.projectHash}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status })
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error updating project status:', error);
                    }
                },

                async createParentTask() {
                    if (!this.newTaskTitle.trim()) return;

                    try {
                        const data = { title: this.newTaskTitle };
                        if (this.newTaskDeadline) {
                            data.deadline = this.newTaskDeadline;
                        }

                        const response = await fetch(`/projects/${this.projectHash}/parent-tasks`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error creating task:', error);
                    }
                },

                async updateParentTask(taskId, title, deadline) {
                    try {
                        const data = { title };
                        if (deadline !== undefined) {
                            data.deadline = deadline || null;
                        }

                        const response = await fetch(`/projects/${this.projectHash}/parent-tasks/${taskId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error updating task:', error);
                    }
                },

                async deleteParentTask(taskId) {
                    try {
                        const response = await fetch(`/projects/${this.projectHash}/parent-tasks/${taskId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error deleting task:', error);
                    }
                },

                async createSubtask(parentId, title, priority, dueDate) {
                    if (!title.trim()) return;

                    try {
                        const response = await fetch(`/projects/${this.projectHash}/parent-tasks/${parentId}/subtasks`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                title,
                                priority,
                                due_date: dueDate || null
                            })
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error creating subtask:', error);
                    }
                },

                async updateSubtask(subtaskId, title, priority, dueDate) {
                    try {
                        const response = await fetch(`/projects/${this.projectHash}/subtasks/${subtaskId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                title,
                                priority,
                                due_date: dueDate || null
                            })
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error updating subtask:', error);
                    }
                },

                async toggleSubtaskComplete(subtaskId, completed) {
                    try {
                        const response = await fetch(`/projects/${this.projectHash}/subtasks/${subtaskId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ completed })
                        });

                        if (response.ok) {
                            const data = await response.json();

                            if (data.project_completed) {
                                triggerConfetti();
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                window.location.reload();
                            }
                        }
                    } catch (error) {
                        console.error('Error toggling subtask:', error);
                    }
                },

                async deleteSubtask(subtaskId) {
                    try {
                        const response = await fetch(`/projects/${this.projectHash}/subtasks/${subtaskId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error deleting subtask:', error);
                    }
                },

                async toggleOnHold(taskId) {
                    try {
                        const response = await fetch(`/projects/${this.projectHash}/parent-tasks/${taskId}/toggle-hold`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error toggling on hold:', error);
                    }
                }
            };
        }
    </script>
</x-app-layout>
