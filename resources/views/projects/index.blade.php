<x-app-layout>
    <x-slot name="sidebar">
        <x-sidebar :projects="$projects" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Projects
            </h2>
            <div class="flex gap-2">
                <button onclick="openNewLabelModal()"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    New Label
                </button>
                <button onclick="openNewProjectModal()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Project
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ activeTab: 'projects' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'projects'"
                            :class="activeTab === 'projects' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Projects
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ $projects->count() }}</span>
                    </button>
                    <button @click="activeTab = 'labels'"
                            :class="activeTab === 'labels' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Labels
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ $labels->count() }}</span>
                    </button>
                </nav>
            </div>

            <!-- Projects Tab -->
            <div x-show="activeTab === 'projects'" x-transition>
                @if($projects->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No projects</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new project.</p>
                        <div class="mt-6">
                            <button onclick="openNewProjectModal()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Project
                            </button>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($projects as $project)
                            <div class="relative overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow h-full flex flex-col {{ $project->label ? $project->label->bg_class . ' ' . $project->label->border_class . ' border' : 'bg-white dark:bg-gray-800' }}">
                                <div class="p-6 flex flex-col flex-1">
                                    <!-- Top section: Title, description, menu -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1 min-w-0 pr-8">
                                            <a href="{{ route('projects.tasks', $project) }}" class="block">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $project->name }}
                                                </h3>
                                            </a>
                                            <div class="mt-1 flex items-start justify-between gap-2 min-h-[2.5rem]">
                                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">
                                                    {{ $project->description ?? '' }}&nbsp;
                                                </p>
                                                @if($project->label)
                                                    <span class="text-xs font-medium {{ $project->label->text_class }} flex-shrink-0 bg-white dark:bg-gray-900 px-1.5 py-0.5 rounded">
                                                        {{ $project->label->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="p-1 rounded-full hover:bg-white/50 dark:hover:bg-gray-700/50">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-transition
                                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-10">
                                                    <a href="{{ route('projects.tasks', $project) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        Open Project
                                                    </a>
                                                    <button onclick="deleteProject('{{ $project->hash }}', '{{ $project->name }}')"
                                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom section: pushed to bottom -->
                                    <div class="mt-auto">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                {{ $project->tasks_count ?? 0 }} tasks
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $project->created_at->diffForHumans() }}
                                            </div>
                                        </div>

                                        <!-- Status Bar -->
                                        <div class="px-3 py-2 rounded-md text-sm font-medium flex items-center gap-2 bg-white/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600">
                                            @if($project->status === 'ready_to_begin')
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($project->status === 'in_progress')
                                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            @elseif($project->status === 'on_hold')
                                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($project->status === 'done')
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                            {{ $project->status_label }}
                                        </div>

                                        <div class="mt-3">
                                            <a href="{{ route('projects.tasks', $project) }}"
                                               class="block text-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/90 rounded-md hover:bg-white dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-600 transition-colors">
                                                Open
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Labels Tab -->
            <div x-show="activeTab === 'labels'" x-transition>
                @if($labels->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No labels</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create labels to organize your projects.</p>
                        <div class="mt-6">
                            <button onclick="openNewLabelModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Label
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($labels as $label)
                                <li class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <div class="flex items-center gap-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $label->bg_class }} {{ $label->text_class }}">
                                            {{ $label->name }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($label->projects_count === 0)
                                                Not used
                                            @elseif($label->projects_count === 1)
                                                Used on 1 project
                                            @else
                                                Used on {{ $label->projects_count }} projects
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="openEditLabelModal({{ $label->id }}, {!! json_encode($label->name) !!}, '{{ $label->color }}')"
                                                class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="confirmDeleteLabel({{ $label->id }}, {!! json_encode($label->name) !!}, {{ $label->projects_count }})"
                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- New Project Modal -->
    <x-modal name="new-project" :show="false" max-width="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Create New Project
            </h2>

            <form id="new-project-form" onsubmit="createProject(event)">
                <div class="mb-4">
                    <x-input-label for="project-name" :value="__('Project Name')" />
                    <x-text-input id="project-name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                    <div id="name-error" class="mt-2 text-sm text-red-600 dark:text-red-400"></div>
                </div>

                <div class="mb-6">
                    <x-input-label for="project-description" :value="__('Description (Optional)')" />
                    <textarea id="project-description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <x-secondary-button onclick="closeNewProjectModal()" type="button">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Create Project
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- New/Edit Label Modal -->
    <x-modal name="label-modal" :show="false" max-width="md">
        <div class="p-6">
            <h2 id="label-modal-title" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Create New Label
            </h2>

            <form id="label-form" onsubmit="saveLabel(event)">
                <input type="hidden" id="label-id" value="">

                <div class="mb-4">
                    <x-input-label for="label-name" :value="__('Label Name')" />
                    <x-text-input id="label-name" name="name" type="text" class="mt-1 block w-full" required maxlength="50" />
                    <div id="label-name-error" class="mt-2 text-sm text-red-600 dark:text-red-400"></div>
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
                                <input type="radio" name="color" value="{{ $color }}" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                <div class="w-8 h-8 rounded-full {{ $bgClass }} peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-{{ $color }}-400 dark:peer-checked:ring-offset-gray-800 transition-all hover:scale-110"></div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <x-secondary-button onclick="closeLabelModal()" type="button">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Save Label
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        function openNewProjectModal() {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'new-project' }));
        }

        function closeNewProjectModal() {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'new-project' }));
        }

        async function createProject(event) {
            event.preventDefault();

            const name = document.getElementById('project-name').value;
            const description = document.getElementById('project-description').value;
            const errorDiv = document.getElementById('name-error');

            errorDiv.textContent = '';

            try {
                const response = await fetch('/projects', {
                    method: 'POST',
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
                        errorDiv.textContent = 'Failed to create project.';
                    }
                    return;
                }

                window.location.reload();
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
            }
        }

        async function deleteProject(hash, name) {
            if (!confirm(`Are you sure you want to delete "${name}"? This will also delete all tasks in this project.`)) {
                return;
            }

            try {
                const response = await fetch(`/projects/${hash}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to delete project.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        // Label functions
        function openNewLabelModal() {
            document.getElementById('label-modal-title').textContent = 'Create New Label';
            document.getElementById('label-id').value = '';
            document.getElementById('label-name').value = '';
            document.querySelector('input[name="color"][value="blue"]').checked = true;
            document.getElementById('label-name-error').textContent = '';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'label-modal' }));
        }

        function openEditLabelModal(id, name, color) {
            document.getElementById('label-modal-title').textContent = 'Edit Label';
            document.getElementById('label-id').value = id;
            document.getElementById('label-name').value = name;
            const colorInput = document.querySelector(`input[name="color"][value="${color}"]`);
            if (colorInput) colorInput.checked = true;
            document.getElementById('label-name-error').textContent = '';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'label-modal' }));
        }

        function closeLabelModal() {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'label-modal' }));
        }

        async function saveLabel(event) {
            event.preventDefault();

            const id = document.getElementById('label-id').value;
            const name = document.getElementById('label-name').value;
            const color = document.querySelector('input[name="color"]:checked').value;
            const errorDiv = document.getElementById('label-name-error');

            errorDiv.textContent = '';

            try {
                const url = id ? `/labels/${id}` : '/labels';
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, color })
                });

                if (!response.ok) {
                    const data = await response.json();
                    if (data.errors?.name) {
                        errorDiv.textContent = data.errors.name[0];
                    } else if (data.errors?.color) {
                        errorDiv.textContent = data.errors.color[0];
                    } else {
                        errorDiv.textContent = 'Failed to save label.';
                    }
                    return;
                }

                window.location.reload();
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
            }
        }

        async function confirmDeleteLabel(id, name, projectCount) {
            let message = `Are you sure you want to delete the label "${name}"?`;
            if (projectCount > 0) {
                message += `\n\nThis label is used on ${projectCount} project${projectCount > 1 ? 's' : ''}. The label will be removed from ${projectCount === 1 ? 'that project' : 'those projects'}.`;
            }

            if (!confirm(message)) {
                return;
            }

            try {
                const response = await fetch(`/labels/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to delete label.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }
    </script>
</x-app-layout>
