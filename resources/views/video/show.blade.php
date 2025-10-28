<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Video Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Video Player -->
                            <div class="aspect-video bg-black rounded-lg mb-4 relative">
                                @if($video->video_path)
                                    <video id="video-player" controls class="w-full h-full rounded-lg" poster="{{ $video->thumbnail_path ? Storage::url($video->thumbnail_path) : '' }}">
                                        <source src="{{ Storage::url($video->video_path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white">
                                        <div class="text-center">
                                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-2">Video not available</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Video Info -->
                            <div class="mb-6">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $video->title }}
                                </h1>
                                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    <div class="flex items-center space-x-4">
                                        <span>{{ number_format($video->views_count) }} views</span>
                                        <span>{{ $video->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button id="like-btn" class="flex items-center space-x-1 {{ $isLiked ? 'text-red-600' : 'text-gray-600 dark:text-gray-400' }} hover:text-red-600">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span id="like-count">{{ $video->likes_count }}</span>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ $video->description }}
                                </p>
                            </div>

                            <!-- Video Actions (Edit/Delete for owner) -->
                            @auth
                                @if($video->user_id === Auth::id())
                                    <div class="flex space-x-2 mb-6">
                                        <a href="{{ route('videos.edit', $video) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            Edit Video
                                        </a>
                                        <form method="POST" action="{{ route('videos.destroy', $video) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this video?')" class="inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 rounded-md shadow-sm text-sm font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900">
                                                Delete Video
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Comments ({{ $video->comments_count }})
                            </h3>

                            @auth
                                <!-- Add Comment Form -->
                                <form method="POST" action="{{ route('comments.store') }}" class="mb-6">
                                    @csrf
                                    <input type="hidden" name="video_id" value="{{ $video->id }}">
                                    <div class="flex space-x-4">
                                        <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full">
                                        <div class="flex-1">
                                            <textarea name="content" rows="3" placeholder="Add a comment..."
                                                      class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                                      required></textarea>
                                            <div class="mt-2 flex justify-end">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                    Comment
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endauth

                            <!-- Comments List -->
                            <div class="space-y-4">
                                @forelse($video->comments as $comment)
                                    <div class="flex space-x-4">
                                        <img src="{{ $comment->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&color=7F9CF5&background=EBF4FF' }}" alt="{{ $comment->user->name }}" class="w-8 h-8 rounded-full">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $comment->content }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No comments yet. Be the first to comment!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Video Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">About this video</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Uploaded by:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $video->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Category:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $video->category->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Duration:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $video->duration ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">File size:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $video->file_size ? number_format($video->file_size / 1024 / 1024, 1) . ' MB' : 'N/A' }}</span>
                                </div>
                                @if($video->tags)
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400 block mb-1">Tags:</span>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(json_decode($video->tags) as $tag)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Related Videos -->
                    @if($relatedVideos->count() > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Related Videos</h3>
                                <div class="space-y-4">
                                    @foreach($relatedVideos as $relatedVideo)
                                        <div class="flex space-x-3">
                                            <div class="flex-shrink-0">
                                                <a href="{{ route('videos.show', $relatedVideo) }}">
                                                    @if($relatedVideo->thumbnail_path)
                                                        <img src="{{ Storage::url($relatedVideo->thumbnail_path) }}" alt="{{ $relatedVideo->title }}" class="w-20 h-12 object-cover rounded">
                                                    @else
                                                        <div class="w-20 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <a href="{{ route('videos.show', $relatedVideo) }}" class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 line-clamp-2">
                                                    {{ $relatedVideo->title }}
                                                </a>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $relatedVideo->user->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($relatedVideo->views_count) }} views</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Like functionality
        document.getElementById('like-btn').addEventListener('click', function() {
            fetch('{{ route("videos.like", $video) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                const likeBtn = document.getElementById('like-btn');
                const likeCount = document.getElementById('like-count');

                if (data.liked) {
                    likeBtn.classList.add('text-red-600');
                    likeBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
                } else {
                    likeBtn.classList.remove('text-red-600');
                    likeBtn.classList.add('text-gray-600', 'dark:text-gray-400');
                }

                likeCount.textContent = data.count;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    </script>
</x-app-layout>
