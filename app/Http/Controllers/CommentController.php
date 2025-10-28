<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'content' => 'required|string|max:1000',
        ]);

        $video = Video::findOrFail($request->video_id);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'video_id' => $request->video_id,
            'content' => $request->content,
            'is_approved' => true, // Auto-approve for now
        ]);

        // Update video comments count
        $video->increment('comments_count');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment->load('user'),
            ]);
        }

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update', $comment);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'Comment updated successfully.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $video = $comment->video;
        $comment->delete();

        // Update video comments count
        $video->decrement('comments_count');

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    /**
     * Get comments for a video (AJAX).
     */
    public function getComments(Video $video)
    {
        $comments = $video->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($comments);
    }
}
