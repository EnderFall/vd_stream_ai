<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Category;
use App\Models\WatchHistory;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Video::where('status', 'published')
            ->where('visibility', 'public')
            ->with(['user', 'category']);

        // Apply sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'trending':
                $query->where('created_at', '>=', now()->subDays(7))
                      ->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Apply category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Apply search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $videos = $query->paginate(12);
        $categories = Category::all();

        return view('video.index', compact('videos', 'categories', 'sort'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('video.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv|max:102400', // 100MB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'visibility' => 'required|in:public,private,unlisted',
        ]);

        $videoPath = $request->file('video_file')->store('videos', 'public');
        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $video = Video::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'video_path' => $videoPath,
            'thumbnail_path' => $thumbnailPath,
            'category_id' => $request->category_id,
            'tags' => $request->tags ? json_encode(explode(',', $request->tags)) : null,
            'visibility' => $request->visibility,
            'status' => 'processing', // Will be updated after processing
            'duration' => '00:00:00', // Will be extracted from video
            'file_size' => $request->file('video_file')->getSize(),
        ]);

        return redirect()->route('videos.show', $video)->with('success', 'Video uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        // Check if user can view this video
        if ($video->visibility === 'private' && $video->user_id !== Auth::id()) {
            abort(403);
        }

        // Increment view count
        $video->increment('views_count');

        // Record watch history
        if (Auth::check()) {
            WatchHistory::updateOrCreate(
                ['user_id' => Auth::id(), 'video_id' => $video->id],
                ['last_watched_at' => now()]
            );
        }

        $video->load(['user', 'category', 'comments.user', 'likes']);
        $relatedVideos = Video::where('category_id', $video->category_id)
            ->where('id', '!=', $video->id)
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->take(8)
            ->get();

        $isLiked = Auth::check() ? $video->likes()->where('user_id', Auth::id())->exists() : false;

        return view('video.show', compact('video', 'relatedVideos', 'isLiked'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        return view('video.edit', compact('video', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'visibility' => 'required|in:public,private,unlisted',
        ]);

        $thumbnailPath = $video->thumbnail_path;

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($thumbnailPath) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $video->update([
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_path' => $thumbnailPath,
            'category_id' => $request->category_id,
            'tags' => $request->tags ? json_encode(explode(',', $request->tags)) : null,
            'visibility' => $request->visibility,
        ]);

        return redirect()->route('videos.show', $video)->with('success', 'Video updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete files
        if ($video->video_path) {
            Storage::disk('public')->delete($video->video_path);
        }
        if ($video->thumbnail_path) {
            Storage::disk('public')->delete($video->thumbnail_path);
        }

        $video->delete();

        return redirect()->route('videos.index')->with('success', 'Video deleted successfully!');
    }

    /**
     * Toggle like on a video
     */
    public function toggleLike(Video $video)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $like = $video->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
            $video->decrement('likes_count');
            return response()->json(['liked' => false, 'count' => $video->likes_count]);
        } else {
            $video->likes()->create(['user_id' => Auth::id()]);
            $video->increment('likes_count');
            return response()->json(['liked' => true, 'count' => $video->likes_count]);
        }
    }

    /**
     * Get videos by category
     */
    public function category(Category $category)
    {
        $videos = Video::where('category_id', $category->id)
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Category::all();

        return view('video.category', compact('videos', 'category', 'categories'));
    }

    /**
     * Get user's watch history
     */
    public function history()
    {
        $videos = WatchHistory::where('user_id', Auth::id())
            ->with('video.user')
            ->orderBy('last_watched_at', 'desc')
            ->paginate(12);

        return view('video.history', compact('videos'));
    }

    /**
     * Get recommended videos
     */
    public function recommended()
    {
        $user = Auth::user();

        // Get categories user has watched
        $watchedCategories = WatchHistory::where('user_id', $user->id)
            ->with('video.category')
            ->get()
            ->pluck('video.category.id')
            ->unique()
            ->filter();

        if ($watchedCategories->isEmpty()) {
            // If no watch history, return popular videos
            $videos = Video::where('status', 'published')
                ->where('visibility', 'public')
                ->orderBy('views_count', 'desc')
                ->paginate(12);
        } else {
            // Get videos from watched categories, excluding already watched
            $watchedVideoIds = WatchHistory::where('user_id', $user->id)
                ->pluck('video_id');

            $videos = Video::where('status', 'published')
                ->where('visibility', 'public')
                ->whereIn('category_id', $watchedCategories)
                ->whereNotIn('id', $watchedVideoIds)
                ->orderBy('views_count', 'desc')
                ->paginate(12);
        }

        return view('video.recommended', compact('videos'));
    }
}
