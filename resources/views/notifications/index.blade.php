<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <!-- Header with actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications Center</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Stay updated with your schedules and VA recommendations</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="toggleDarkMode()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-moon"></i> Toggle Theme
                            </button>
                            <button onclick="markAllRead()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-check-double"></i> Mark All Read
                            </button>
                        </div>
                    </div>

                    <!-- Notification Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm">Total Notifications</p>
                                    <p class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-bell text-3xl opacity-75"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-100 text-sm">Unread</p>
                                    <p class="text-2xl font-bold">{{ $stats['unread'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-envelope text-3xl opacity-75"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm">This Week</p>
                                    <p class="text-2xl font-bold">{{ $stats['this_week'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-calendar-week text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="space-y-4">
                        @forelse($notifications ?? [] as $notification)
                            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-6 {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-l-blue-500' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div class="flex-shrink-0">
                                                @switch($notification->type)
                                                    @case('schedule_reminder')
                                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-calendar text-blue-600 dark:text-blue-400"></i>
                                                        </div>
                                                        @break
                                                    @case('va_recommendation')
                                                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-robot text-purple-600 dark:text-purple-400"></i>
                                                        </div>
                                                        @break
                                                    @case('target_achievement')
                                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-trophy text-green-600 dark:text-green-400"></i>
                                                        </div>
                                                        @break
                                                    @default
                                                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-bell text-gray-600 dark:text-gray-400"></i>
                                                        </div>
                                                @endswitch
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>

                                        @if(isset($notification->data['actions']) && is_array($notification->data['actions']))
                                            <div class="mt-4 flex space-x-3">
                                                @foreach($notification->data['actions'] as $action)
                                                    <a href="{{ $action['url'] ?? '#' }}"
                                                       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                        {{ $action['label'] ?? 'View' }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-2 ml-4">
                                        @if(!$notification->read_at)
                                            <button onclick="markAsRead({{ $notification->id }})"
                                                    class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors">
                                                <i class="fas fa-check"></i> Mark Read
                                            </button>
                                        @endif
                                        <button onclick="deleteNotification({{ $notification->id }})"
                                                class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-bell-slash text-4xl mb-4"></i>
                                <p class="text-lg">No notifications yet</p>
                                <p class="text-sm">You'll see notifications for schedules, VA recommendations, and more here</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if(isset($notifications) && $notifications->hasPages())
                        <div class="mt-8">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function markAsRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function markAllRead() {
            if (confirm('Mark all notifications as read?')) {
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }

        function deleteNotification(id) {
            if (confirm('Delete this notification?')) {
                fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }
    </script>
</x-app-layout>
