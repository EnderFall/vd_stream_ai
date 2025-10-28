<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AI Virtual Assistant Recommendations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Generate Recommendations Button -->
                    <div class="mb-6">
                        <form method="POST" action="{{ route('va.generate') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Generate New Recommendations
                            </button>
                        </form>
                    </div>

                    <!-- Recommendations List -->
                    @if($recommendations->count() > 0)
                        <div class="space-y-4">
                            @foreach($recommendations as $recommendation)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    Recommended Schedule: {{ $recommendation->recommended_start->format('M d, Y H:i') }}
                                                </h3>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($recommendation->confidence_score >= 0.8) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($recommendation->confidence_score >= 0.6) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                    {{ number_format($recommendation->confidence_score * 100, 1) }}% Confidence
                                                </span>
                                            </div>
                                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                                {{ $recommendation->reason }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Generated {{ $recommendation->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="ml-4">
                                            <form method="POST" action="{{ route('schedules.store') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="title" value="AI Recommended Schedule">
                                                <input type="hidden" name="description" value="{{ $recommendation->reason }}">
                                                <input type="hidden" name="start_at" value="{{ $recommendation->recommended_start->format('Y-m-d H:i:s') }}">
                                                <input type="hidden" name="end_at" value="{{ $recommendation->recommended_start->addHours(2)->format('Y-m-d H:i:s') }}">
                                                <input type="hidden" name="platform" value="General">
                                                <input type="hidden" name="estimated_income" value="0">
                                                <input type="hidden" name="status" value="pending">
                                                <input type="hidden" name="visibility" value="private">
                                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Add to Schedule
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $recommendations->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No AI recommendations yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Generate your first AI recommendations to optimize your scheduling.
                            </p>
                            <div class="mt-6">
                                <form method="POST" action="{{ route('va.generate') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                        Generate AI Recommendations
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
