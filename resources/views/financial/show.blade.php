<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <!-- Header with actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction #{{ $transaction->id }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Created on {{ $transaction->created_at->format('M d, Y \a\t H:i') }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('financial.edit', ['id' => $transaction->id]) }}"
                               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('financial.index') }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ $transaction->id }}">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this transaction?')"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                            <a href="{{ route('financial.index') }}"
                               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h4>
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Type:</span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($transaction->type === 'income') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Category:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($transaction->category) }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount:</span>
                                        <span class="text-lg font-semibold
                                            @if($transaction->type === 'income') text-green-600 dark:text-green-400
                                            @else text-red-600 dark:text-red-400 @endif">
                                            {{ $transaction->type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Date:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $transaction->date->format('M d, Y') }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $transaction->updated_at->format('M d, Y \a\t H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($transaction->description)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h4>
                                <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    {{ $transaction->description }}
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- Related Information -->
                        <div class="space-y-6">
                            <!-- Schedule Link -->
                            @if($transaction->schedule)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Schedule</h4>
                                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h5 class="font-medium text-blue-900 dark:text-blue-100">{{ $transaction->schedule->title }}</h5>
                                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                                {{ $transaction->schedule->start_at->format('M d, Y H:i') }} - {{ $transaction->schedule->platform }}
                                            </p>
                                        </div>
                                        <a href="{{ route('schedules.show', $transaction->schedule) }}"
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Receipt -->
                            @if($transaction->receipt_url)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Receipt</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <a href="{{ asset('storage/' . $transaction->receipt_url) }}" target="_blank"
                                       class="flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        View Receipt
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Tags -->
                            @if($transaction->tags)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tags</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(json_decode($transaction->tags, true) ?? [] as $tag)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('financial.edit', ['id' => $transaction->id]) }}"
                           class="inline-flex items-center px-6 py-3 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-yellow-700 focus:outline-none focus:border-yellow-900 focus:ring focus:ring-yellow-300 disabled:opacity-25 transition">
                            <i class="fas fa-edit mr-2"></i> Edit Transaction
                        </a>

                        <form method="POST" action="{{ route('financial.index') }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" value="{{ $transaction->id }}">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this transaction? This action cannot be undone.')"
                                    class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                                <i class="fas fa-trash mr-2"></i> Delete Transaction
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
