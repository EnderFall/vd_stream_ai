<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $playlists = Playlist::where('user_id', Auth::id())
            ->withCount('videos')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('playlist.index', compact('playlists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('playlist.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
        ]);

        Playlist::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility,
        ]);

        return redirect()->route('playlists.index')->with('success', 'Playlist created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Playlist $playlist)
    {
        if ($playlist->visibility === 'private' && $playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $playlist->load(['videos', 'user']);
        return view('playlist.show', compact('playlist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Playlist $playlist)
    {
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        return view('playlist.edit', compact('playlist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Playlist $playlist)
    {
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
        ]);

        $playlist->update([
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility,
        ]);

        return redirect()->route('playlists.show', $playlist)->with('success', 'Playlist updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playlist $playlist)
    {
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $playlist->delete();

        return redirect()->route('playlists.index')->with('success', 'Playlist deleted successfully!');
    }

    /**
     * Add video to playlist
     */
    public function addVideo(Request $request, Playlist $playlist)
    {
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'video_id' => 'required|exists:videos,id',
        ]);

        $playlist->videos()->syncWithoutDetaching([$request->video_id]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove video from playlist
     */
    public function removeVideo(Playlist $playlist, Video $video)
    {
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $playlist->videos()->detach($video->id);

        return response()->json(['success' => true]);
    }
}
