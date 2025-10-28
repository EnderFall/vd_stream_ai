<?php

namespace App\Http\Controllers;

use App\Models\MonthlyTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $targets = MonthlyTarget::where('user_id', Auth::id())
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($target) {
                // Calculate actual income for this target
                $startDate = \Carbon\Carbon::create($target->year, $target->month, 1)->startOfMonth();
                $endDate = \Carbon\Carbon::create($target->year, $target->month, 1)->endOfMonth();

                $actualIncome = \App\Models\Transaction::where('user_id', Auth::id())
                    ->where('type', 'income')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->sum('amount');

                $target->actual_income = $actualIncome;
                $target->progress_percentage = $target->target_amount > 0
                    ? min(100, round(($actualIncome / $target->target_amount) * 100))
                    : 0;

                return $target;
            });

        // Current month target and progress
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $currentTarget = MonthlyTarget::where('user_id', Auth::id())
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();

        $currentProgress = 0;
        $progressPercentage = 0;

        if ($currentTarget) {
            $startDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
            $endDate = now();

            $currentProgress = \App\Models\Transaction::where('user_id', Auth::id())
                ->where('type', 'income')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');

            $progressPercentage = $currentTarget->target_amount > 0
                ? min(100, round(($currentProgress / $currentTarget->target_amount) * 100))
                : 0;
        }

        return view('targets.index', compact('targets', 'currentTarget', 'currentProgress', 'progressPercentage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('targets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'target_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        // Check if target already exists for this month/year
        $existing = MonthlyTarget::where('user_id', Auth::id())
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Target already exists for this month/year.'], 422);
            }
            return back()->withErrors(['month' => 'Target already exists for this month/year.']);
        }

        $target = MonthlyTarget::create([
            'user_id' => Auth::id(),
            'year' => $request->year,
            'month' => $request->month,
            'target_amount' => $request->target_amount,
            'note' => $request->note,
        ]);

        // Create notification for new target
        $target->user->notifications()->create([
            'type' => 'target_set',
            'data' => [
                'title' => 'New Monthly Target Set',
                'message' => "You've set a target of $" . number_format($target->target_amount) . " for " . \Carbon\Carbon::create($target->year, $target->month)->format('F Y'),
                'target_id' => $target->id,
                'actions' => [
                    [
                        'label' => 'View Targets',
                        'url' => route('targets.index')
                    ]
                ]
            ]
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'target' => $target]);
        }

        return redirect()->route('targets.index')->with('success', 'Monthly target created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $target = MonthlyTarget::where('user_id', Auth::id())->findOrFail($id);
        return view('targets.show', compact('target'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $target = MonthlyTarget::where('user_id', Auth::id())->findOrFail($id);
        return view('targets.edit', compact('target'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $target = MonthlyTarget::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'target_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $target->update($request->only(['target_amount', 'note']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'target' => $target]);
        }

        return redirect()->route('targets.index')->with('success', 'Monthly target updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $target = MonthlyTarget::where('user_id', Auth::id())->findOrFail($id);
        $target->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('targets.index')->with('success', 'Monthly target deleted successfully.');
    }

    /**
     * Get target data for AJAX requests.
     */
    public function showAjax($id)
    {
        $target = MonthlyTarget::where('user_id', Auth::id())->findOrFail($id);

        // Calculate actual income for this target
        $startDate = \Carbon\Carbon::create($target->year, $target->month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::create($target->year, $target->month, 1)->endOfMonth();

        $actualIncome = \App\Models\Transaction::where('user_id', Auth::id())
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        return response()->json([
            'id' => $target->id,
            'year' => $target->year,
            'month' => $target->month,
            'target_amount' => $target->target_amount,
            'note' => $target->note,
            'actual_income' => $actualIncome,
            'progress_percentage' => $target->target_amount > 0
                ? min(100, round(($actualIncome / $target->target_amount) * 100))
                : 0,
        ]);
    }
}
