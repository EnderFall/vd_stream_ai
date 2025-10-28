<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Financial Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <!-- Header with actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Management</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Track your income and expenses</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="toggleDarkMode()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-moon"></i> Toggle Theme
                            </button>
                            <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus"></i> Add Transaction
                            </button>
                            <a href="{{ route('financial.export', ['format' => 'pdf']) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('financial.export', ['format' => 'csv']) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                        </div>
                    </div>

                    <!-- Financial Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm">Total Income</p>
                                    <p class="text-2xl font-bold">${{ number_format($summary['total_income'] ?? 0, 2) }}</p>
                                </div>
                                <i class="fas fa-arrow-up text-3xl opacity-75"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-100 text-sm">Total Expenses</p>
                                    <p class="text-2xl font-bold">${{ number_format($summary['total_expenses'] ?? 0, 2) }}</p>
                                </div>
                                <i class="fas fa-arrow-down text-3xl opacity-75"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm">Net Profit</p>
                                    <p class="text-2xl font-bold">${{ number_format(($summary['total_income'] ?? 0) - ($summary['total_expenses'] ?? 0), 2) }}</p>
                                </div>
                                <i class="fas fa-chart-line text-3xl opacity-75"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm">Target Progress</p>
                                    <p class="text-2xl font-bold">{{ $targetProgress ?? 0 }}%</p>
                                </div>
                                <i class="fas fa-bullseye text-3xl opacity-75"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="bg-white dark:bg-gray-900 rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h4>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($transactions ?? [] as $transaction)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $transaction->date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($transaction->type === 'income') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $transaction->category }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                {{ $transaction->description ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium
                                                @if($transaction->type === 'income') text-green-600 dark:text-green-400
                                                @else text-red-600 dark:text-red-400 @endif">
                                                {{ $transaction->type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button onclick="editTransaction({{ $transaction->id }})" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteTransaction({{ $transaction->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-receipt text-4xl mb-4"></i>
                                                <p>No transactions found</p>
                                                <p class="text-sm">Add your first transaction to get started</p>
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

    <!-- Create/Edit Transaction Modal -->
    <div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modalTitle">Add Transaction</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="transactionForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="transactionId" name="id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select id="type" name="type" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Type</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select id="category" name="category" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Category</option>
                            <optgroup label="Income">
                                <option value="sponsor">Sponsor</option>
                                <option value="donation">Donation</option>
                                <option value="ads">Ads/Revenue</option>
                                <option value="merch">Merchandise</option>
                            </optgroup>
                            <optgroup label="Expense">
                                <option value="equipment">Equipment</option>
                                <option value="software">Software</option>
                                <option value="marketing">Marketing</option>
                                <option value="other">Other</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                        <input type="number" id="amount" name="amount" step="0.01" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                        <input type="date" id="date" name="date" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Save Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add Transaction';
            document.getElementById('transactionForm').reset();
            document.getElementById('transactionId').value = '';
            document.getElementById('transactionModal').classList.remove('hidden');
        }

        function editTransaction(id) {
            // Fetch transaction data and populate form
            fetch(`/financial/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Transaction';
                    document.getElementById('transactionId').value = data.id;
                    document.getElementById('type').value = data.type;
                    document.getElementById('category').value = data.category;
                    document.getElementById('amount').value = data.amount;
                    document.getElementById('date').value = data.date;
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('transactionModal').classList.remove('hidden');
                });
        }

        function deleteTransaction(id) {
            if (confirm('Are you sure you want to delete this transaction?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/financial/${id}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        }

        // Handle form submission
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = formData.get('id');
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/financial/${id}` : '/financial';

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
                    alert('Error saving transaction');
                }
            });
        });
    </script>
</x-app-layout>
