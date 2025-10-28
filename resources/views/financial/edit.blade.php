<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('financial.index') }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $transaction->id }}">

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction Type</label>
                            <select id="type" name="type" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Type</option>
                                <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <select id="category" name="category" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Category</option>
                                <optgroup label="Income">
                                    <option value="sponsor" {{ old('category', $transaction->category) == 'sponsor' ? 'selected' : '' }}>Sponsor</option>
                                    <option value="donation" {{ old('category', $transaction->category) == 'donation' ? 'selected' : '' }}>Donation</option>
                                    <option value="ads" {{ old('category', $transaction->category) == 'ads' ? 'selected' : '' }}>Ads/Revenue</option>
                                    <option value="merch" {{ old('category', $transaction->category) == 'merch' ? 'selected' : '' }}>Merchandise</option>
                                </optgroup>
                                <optgroup label="Expense">
                                    <option value="equipment" {{ old('category', $transaction->category) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                    <option value="software" {{ old('category', $transaction->category) == 'software' ? 'selected' : '' }}>Software</option>
                                    <option value="marketing" {{ old('category', $transaction->category) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="other" {{ old('category', $transaction->category) == 'other' ? 'selected' : '' }}>Other</option>
                                </optgroup>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount ($)</label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount', $transaction->amount) }}" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                            <input type="date" id="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description', $transaction->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-6">
                            <a href="{{ route('financial.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-900 dark:focus:border-gray-700 active:bg-gray-900 dark:active:bg-gray-300 focus:ring focus:ring-gray-300 dark:focus:ring-gray-600 disabled:opacity-25 transition">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Update Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
