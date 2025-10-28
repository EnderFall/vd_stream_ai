<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Category;
use App\Models\WatchHistory;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Get featured videos (most viewed)
        $featuredVideos = Video::where('status', 'published')
            ->where('visibility', 'public')
            ->orderBy('views_count', 'desc')
            ->take(6)
            ->get();

        // Get trending videos (most recent with high engagement)
        $trendingVideos = Video::where('status', 'published')
            ->where('visibility', 'public')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('views_count', 'desc')
            ->take(8)
            ->get();

        // Get categories with video counts
        $categories = Category::withCount(['videos' => function ($query) {
            $query->where('status', 'published')->where('visibility', 'public');
        }])->get();

        // Get user's watch history (recently watched)
        $recentlyWatched = WatchHistory::where('user_id', $user->id)
            ->with('video')
            ->orderBy('last_watched_at', 'desc')
            ->take(6)
            ->get()
            ->pluck('video')
            ->filter();

        // Get user's playlists
        $userPlaylists = Playlist::where('user_id', $user->id)
            ->withCount('videos')
            ->take(4)
            ->get();

        // Get recommended videos based on watch history
        $recommendedVideos = $this->getRecommendedVideos($user);

        return view('main.dashboard', compact(
            'featuredVideos',
            'trendingVideos',
            'categories',
            'recentlyWatched',
            'userPlaylists',
            'recommendedVideos'
        ));
    }

    private function getRecommendedVideos($user)
    {
        // Get categories user has watched
        $watchedCategories = WatchHistory::where('user_id', $user->id)
            ->with('video.category')
            ->get()
            ->pluck('video.category.id')
            ->unique()
            ->filter();

        if ($watchedCategories->isEmpty()) {
            // If no watch history, return popular videos
            return Video::where('status', 'published')
                ->where('visibility', 'public')
                ->orderBy('views_count', 'desc')
                ->take(8)
                ->get();
        }

        // Get videos from watched categories, excluding already watched
        $watchedVideoIds = WatchHistory::where('user_id', $user->id)
            ->pluck('video_id');

        return Video::where('status', 'published')
            ->where('visibility', 'public')
            ->whereIn('category_id', $watchedCategories)
            ->whereNotIn('id', $watchedVideoIds)
            ->orderBy('views_count', 'desc')
            ->take(8)
            ->get();
    }
}
