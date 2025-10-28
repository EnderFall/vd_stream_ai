<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::where('user_id', Auth::id())
            ->orderBy('start_at', 'asc')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->start_at->toISOString(),
                    'end' => $schedule->end_at->toISOString(),
                    'description' => $schedule->description,
                    'extendedProps' => [
                        'platform' => $schedule->platform,
                        'estimated_income' => $schedule->estimated_income,
                        'status' => $schedule->status,
                    ],
                ];
            });

        $upcomingSchedules = Schedule::where('user_id', Auth::id())
            ->where('start_at', '>', now())
            ->where('start_at', '<=', now()->addDays(7))
            ->orderBy('start_at', 'asc')
            ->take(5)
            ->get();

        return view('schedules.index', compact('schedules', 'upcomingSchedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('schedules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'estimated_income' => 'nullable|numeric|min:0',
        ]);

        $schedule = Schedule::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'platform' => $request->platform,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'estimated_income' => $request->estimated_income,
            'status' => 'pending',
            'visibility' => 'private',
        ]);

        // Create notification for new schedule
        $schedule->user->notifications()->create([
            'type' => 'schedule_reminder',
            'payload' => json_encode([
                'title' => 'New Schedule Created',
                'message' => "Your schedule '{$schedule->title}' has been created for " . $schedule->start_at->format('M d, Y H:i'),
                'schedule_id' => $schedule->id,
                'actions' => [
                    [
                        'label' => 'View Schedule',
                        'url' => route('schedules.index')
                    ]
                ]
            ])
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'schedule' => $schedule]);
        }

        return redirect()->route('schedules.index')->with('success', 'Schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::where('user_id', Auth::id())->findOrFail($id);
        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = Schedule::where('user_id', Auth::id())->findOrFail($id);
        return view('schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = Schedule::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'estimated_income' => 'nullable|numeric|min:0',
        ]);

        $schedule->update($request->only([
            'title', 'description', 'platform', 'start_at', 'end_at',
            'estimated_income'
        ]));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'schedule' => $schedule]);
        }

        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::where('user_id', Auth::id())->findOrFail($id);
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully.');
    }

    /**
     * Get schedules for FullCalendar.
     */
    public function calendar()
    {
        $schedules = Schedule::where('user_id', Auth::id())->get();

        $events = $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->start_at->toISOString(),
                'end' => $schedule->end_at->toISOString(),
                'description' => $schedule->description,
                'extendedProps' => [
                    'platform' => $schedule->platform,
                    'estimated_income' => $schedule->estimated_income,
                    'status' => $schedule->status,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Get schedule data for AJAX requests.
     */
    public function showAjax($id)
    {
        $schedule = Schedule::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'id' => $schedule->id,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'platform' => $schedule->platform,
            'start_at' => $schedule->start_at->format('Y-m-d\TH:i'),
            'end_at' => $schedule->end_at->format('Y-m-d\TH:i'),
            'estimated_income' => $schedule->estimated_income,
            'status' => $schedule->status,
        ]);
    }
}
