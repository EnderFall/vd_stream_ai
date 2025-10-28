<x-app-layout title="Monthly Targets">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monthly Targets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <!-- Header with actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Income Targets</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Set and track your monthly financial goals</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="toggleDarkMode()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-moon"></i> Toggle Theme
                            </button>
                            <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus"></i> Set Target
                            </button>
                        </div>
                    </div>

                    <!-- Current Month Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm">Current Month Target</p>
                                    <p class="text-2xl font-bold">${{ number_format($currentTarget->target_amount ?? 0, 2) }}</p>
                                </div>
                                <i class="fas fa-bullseye text-3xl opacity-75"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm">Current Progress</p>
                                    <p class="text-2xl font-bold">${{ number_format($currentProgress ?? 0, 2) }}</p>
                                </div>
                                <i class="fas fa-chart-line text-3xl opacity-75"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm">Progress Percentage</p>
                                    <p class="text-2xl font-bold">{{ $progressPercentage ?? 0 }}%</p>
                                </div>
                                <i class="fas fa-percentage text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @if($currentTarget)
                        <div class="mb-8">
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-2">
                                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-4 rounded-full transition-all duration-500"
                                     style="width: {{ min($progressPercentage ?? 0, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>${{ number_format($currentProgress ?? 0, 2) }} earned</span>
                                <span>${{ number_format($currentTarget->target_amount, 2) }} target</span>
                            </div>
                        </div>
                    @endif

                    <!-- Targets History -->
                    <div class="bg-white dark:bg-gray-900 rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Target History</h4>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Month/Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Target Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actual Income</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($targets ?? [] as $target)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::create($target->year, $target->month)->format('F Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                ${{ number_format($target->target_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                ${{ number_format($target->actual_income ?? 0, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $target->progress_percentage ?? 0 }}%
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if(($target->progress_percentage ?? 0) >= 100) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif(($target->progress_percentage ?? 0) >= 75) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                    @if(($target->progress_percentage ?? 0) >= 100) Achieved
                                                    @elseif(($target->progress_percentage ?? 0) >= 75) On Track
                                                    @else Behind @endif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button onclick="editTarget({{ $target->id }})" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteTarget({{ $target->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-target text-4xl mb-4"></i>
                                                <p>No targets set yet</p>
                                                <p class="text-sm">Set your first monthly target to start tracking progress</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Target Modal -->
    <div id="targetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modalTitle">Set Monthly Target</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="targetForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="targetId" name="id">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
                            <select id="month" name="month" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Month</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                            <select id="year" name="year" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Year</option>
                                @for($y = date('Y'); $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target Amount ($)</label>
                        <input type="number" id="target_amount" name="target_amount" step="0.01" min="0" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                        <textarea id="note" name="note" rows="3"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Additional notes about this target..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Save Target
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Set Monthly Target';
            document.getElementById('targetForm').reset();
            document.getElementById('targetId').value = '';

            // Set current month/year as default
            const now = new Date();
            document.getElementById('month').value = now.getMonth() + 1;
            document.getElementById('year').value = now.getFullYear();

            document.getElementById('targetModal').classList.remove('hidden');
        }

        function editTarget(id) {
            // Fetch target data and populate form
            fetch(`/targets/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Target';
                    document.getElementById('targetId').value = data.id;
                    document.getElementById('month').value = data.month;
                    document.getElementById('year').value = data.year;
                    document.getElementById('target_amount').value = data.target_amount;
                    document.getElementById('note').value = data.note || '';
                    document.getElementById('targetModal').classList.remove('hidden');
                });
        }

        function deleteTarget(id) {
            if (confirm('Are you sure you want to delete this target?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/targets/${id}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('targetModal').classList.add('hidden');
        }

        // Handle form submission
        document.getElementById('targetForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = formData.get('id');
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/targets/${id}` : '/targets';

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert('Error saving target');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the target');
            });
        });
    </script>
</x-app-layout>
