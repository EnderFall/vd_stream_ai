@extends('layouts.app')

@section('title', 'VideoStream - Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section with Featured Video -->
    @if($featuredVideos->count() > 0)
        <div class="relative bg-gradient-to-r from-blue-600 to-purple-700 text-white">
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">
                        Welcome to VideoStream
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-blue-100">
                        Discover amazing videos from creators around the world
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="#featured" class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-colors">
                            Explore Videos
                        </a>
                        <a href="{{ route('videos.create') }}" class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                            Upload Video
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Categories Navigation -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Browse by Category</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('videos.index') }}" class="bg-white px-6 py-3 rounded-full shadow-sm hover:shadow-md transition-shadow border border-gray-200">
                    <i class="fas fa-th-large mr-2 text-gray-600"></i>
                    All Videos
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('videos.category', $category->id) }}" class="bg-white px-6 py-3 rounded-full shadow-sm hover:shadow-md transition-shadow border border-gray-200">
                        <i class="fas fa-tag mr-2 text-gray-600"></i>
                        {{ $category->name }}
                        <span class="ml-2 bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                            {{ $category->videos_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Featured Videos -->
        @if($featuredVideos->count() > 0)
            <section id="featured" class="mb-16">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-star text-yellow-500 mr-3"></i>
                        Featured Videos
                    </h2>
                    <a href="{{ route('videos.index', ['sort' => 'popular']) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredVideos as $video)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            <div class="relative">
                                @if($video->thumbnail_path)
                                    <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                        <i class="fas fa-play-circle text-white text-4xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                                    <a href="{{ route('videos.show', $video) }}" class="bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-900 px-6 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-play mr-2"></i> Watch Now
                                    </a>
                                </div>
                                <div class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-sm">
                                    <i class="fas fa-eye mr-1"></i> {{ number_format($video->views_count) }}
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">
                                    <a href="{{ route('videos.show', $video) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $video->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $video->description }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                                            {{ substr($video->user->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $video->user->name }}</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $video->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Trending Videos -->
        @if($trendingVideos->count() > 0)
            <section class="mb-16">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-fire text-red-500 mr-3"></i>
                        Trending Now
                    </h2>
                    <a href="{{ route('videos.index', ['sort' => 'trending']) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($trendingVideos as $video)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="relative">
                                @if($video->thumbnail_path)
                                    <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-32 object-cover">
                                @else
                                    <div class="w-full h-32 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                        <i class="fas fa-play-circle text-white text-2xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                    <a href="{{ route('videos.show', $video) }}" class="text-white opacity-0 hover:opacity-100 transition-opacity">
                                        <i class="fas fa-play-circle text-3xl"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 text-sm">
                                    <a href="{{ route('videos.show', $video) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $video->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-gray-600 mb-2">{{ $video->user->name }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span><i class="fas fa-eye mr-1"></i> {{ number_format($video->views_count) }}</span>
                                    <span>{{ $video->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Recently Watched -->
        @if($recentlyWatched->count() > 0)
            <section class="mb-16">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-history text-blue-500 mr-3"></i>
                        Continue Watching
                    </h2>
                    <a href="{{ route('videos.history') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recentlyWatched as $video)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="relative">
                                @if($video->thumbnail_path)
                                    <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-40 object-cover">
                                @else
                                    <div class="w-full h-40 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                        <i class="fas fa-play-circle text-white text-3xl"></i>
                                    </div>
                                @endif
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                                    <div class="w-full bg-gray-300 rounded-full h-1 mb-2">
                                        <div class="bg-blue-600 h-1 rounded-full" style="width: 60%"></div>
                                    </div>
                                    <a href="{{ route('videos.show', $video) }}" class="text-white hover:text-blue-300 transition-colors">
                                        <i class="fas fa-play mr-2"></i> Continue
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">
                                    <a href="{{ route('videos.show', $video) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $video->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">{{ $video->user->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Recommended Videos -->
        @if($recommendedVideos->count() > 0)
            <section class="mb-16">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-thumbs-up text-green-500 mr-3"></i>
                        Recommended for You
                    </h2>
                    <a href="{{ route('videos.recommended') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($recommendedVideos as $video)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="relative">
                                @if($video->thumbnail_path)
                                    <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-32 object-cover">
                                @else
                                    <div class="w-full h-32 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                        <i class="fas fa-play-circle text-white text-2xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                    <a href="{{ route('videos.show', $video) }}" class="text-white opacity-0 hover:opacity-100 transition-opacity">
                                        <i class="fas fa-play-circle text-3xl"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 text-sm">
                                    <a href="{{ route('videos.show', $video) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $video->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-gray-600 mb-2">{{ $video->user->name }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span><i class="fas fa-eye mr-1"></i> {{ number_format($video->views_count) }}</span>
                                    <span>{{ $video->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- User's Playlists -->
        @if($userPlaylists->count() > 0)
            <section class="mb-16">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-list text-purple-500 mr-3"></i>
                        Your Playlists
                    </h2>
                    <a href="{{ route('playlists.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($userPlaylists as $playlist)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="bg-gradient-to-br from-purple-500 to-pink-500 h-32 flex items-center justify-center">
                                <i class="fas fa-list text-white text-3xl"></i>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('playlists.show', $playlist) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $playlist->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">{{ $playlist->videos_count }} videos</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $playlist->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Quick Actions -->
        <section class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
                <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                Quick Actions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('videos.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-upload text-blue-600 text-4xl mb-4"></i>
                    <h3 class="font-bold text-gray-900 mb-2">Upload Video</h3>
                    <p class="text-gray-600 text-center text-sm">Share your content with the world</p>
                </a>
                <a href="{{ route('playlists.create') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-plus text-green-600 text-4xl mb-4"></i>
                    <h3 class="font-bold text-gray-900 mb-2">Create Playlist</h3>
                    <p class="text-gray-600 text-center text-sm">Organize your favorite videos</p>
                </a>
                <a href="{{ route('videos.index') }}" class="flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-search text-purple-600 text-4xl mb-4"></i>
                    <h3 class="font-bold text-gray-900 mb-2">Browse Videos</h3>
                    <p class="text-gray-600 text-center text-sm">Discover new content</p>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
