<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Smart Scheduler') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <!-- Header with actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Schedule Management</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage your streaming schedules and content planning</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="toggleDarkMode()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-moon"></i> Toggle Theme
                            </button>
                            <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus"></i> New Schedule
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Container -->
                    <div class="bg-white dark:bg-gray-900 rounded-lg p-4">
                        <div id="calendar" class="w-full h-96"></div>
                    </div>

                    <!-- Schedule List -->
                    <div class="mt-8">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Upcoming Schedules</h4>
                        <div class="space-y-3">
                            @forelse($upcomingSchedules ?? [] as $schedule)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex justify-between items-center">
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white">{{ $schedule->title }}</h5>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $schedule->platform }} • {{ $schedule->start_at->format('M d, Y H:i') }}</p>
                                        <span class="inline-block px-2 py-1 text-xs rounded-full
                                            @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($schedule->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="editSchedule({{ $schedule->id }})" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteSchedule({{ $schedule->id }})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                    <p>No upcoming schedules</p>
                                    <p class="text-sm">Create your first schedule to get started</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Schedule Modal -->
    <div id="scheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modalTitle">Create Schedule</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="scheduleForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="scheduleId" name="id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text" id="title" name="title" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Platform</label>
                            <select id="platform" name="platform" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Platform</option>
                                <option value="YouTube">YouTube</option>
                                <option value="Twitch">Twitch</option>
                                <option value="TikTok">TikTok</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Facebook">Facebook</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Income</label>
                            <input type="number" id="estimated_income" name="estimated_income" step="0.01"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                            <input type="datetime-local" id="start_at" name="start_at" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                            <input type="datetime-local" id="end_at" name="end_at" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Save Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let calendar;

        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
        });

        function initializeCalendar() {
            const calendarEl = document.getElementById('calendar');

            calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: @json($schedules ?? []),
                editable: true,
                selectable: true,
                select: function(info) {
                    openCreateModal();
                    document.getElementById('start_at').value = info.startStr.slice(0, 16);
                    document.getElementById('end_at').value = info.endStr.slice(0, 16);
                },
                eventClick: function(info) {
                    editSchedule(info.event.id);
                }
            });

            calendar.render();
        }

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Create Schedule';
            document.getElementById('scheduleForm').reset();
            document.getElementById('scheduleId').value = '';
            document.getElementById('scheduleModal').classList.remove('hidden');
        }

        function editSchedule(id) {
            // Fetch schedule data and populate form
            fetch(`/schedules/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Schedule';
                    document.getElementById('scheduleId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('description').value = data.description;
                    document.getElementById('platform').value = data.platform;
                    document.getElementById('estimated_income').value = data.estimated_income;
                    document.getElementById('start_at').value = data.start_at.slice(0, 16);
                    document.getElementById('end_at').value = data.end_at.slice(0, 16);
                    document.getElementById('scheduleModal').classList.remove('hidden');
                });
        }

        function deleteSchedule(id) {
            if (confirm('Are you sure you want to delete this schedule?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/schedules/${id}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
        }

        // Handle form submission
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = formData.get('id');
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/schedules/${id}` : '/schedules';

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert('Error saving schedule');
                }
            });
        });
    </script>
</x-app-layout>
