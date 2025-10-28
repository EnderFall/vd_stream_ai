<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload Video') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Title
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 @error('title') border-red-500 @enderror"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 @error('description') border-red-500 @enderror"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Category
                            </label>
                            <select name="category_id" id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 @error('category_id') border-red-500 @enderror"
                                    required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-4">
                            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tags (comma separated)
                            </label>
                            <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                                   placeholder="e.g. tutorial, laravel, php"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 @error('tags') border-red-500 @enderror">
                            @error('tags')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Visibility -->
                        <div class="mb-4">
                            <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Visibility
                            </label>
                            <select name="visibility" id="visibility"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 @error('visibility') border-red-500 @enderror"
                                    required>
                                <option value="public" {{ old('visibility', 'public') == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Private</option>
                                <option value="unlisted" {{ old('visibility') == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                            </select>
                            @error('visibility')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Video File -->
                        <div class="mb-4">
                            <label for="video_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Video File
                            </label>
                            <input type="file" name="video_file" id="video_file" accept="video/*"
                                   class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-indigo-50 file:text-indigo-700
                                          dark:file:bg-indigo-900 dark:file:text-indigo-300
                                          hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800 @error('video_file') border-red-500 @enderror"
                                   required>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Max file size: 100MB. Supported formats: MP4, MOV, AVI, WMV</p>
                            @error('video_file')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thumbnail -->
                        <div class="mb-6">
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Thumbnail (Optional)
                            </label>
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-indigo-50 file:text-indigo-700
                                          dark:file:bg-indigo-900 dark:file:text-indigo-300
                                          hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800 @error('thumbnail') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Max file size: 5MB. Supported formats: JPEG, PNG, JPG, GIF</p>
                            @error('thumbnail')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('videos.index') }}" class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Upload Video
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
