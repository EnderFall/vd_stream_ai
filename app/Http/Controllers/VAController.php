<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\MonthlyTarget;
use App\Models\VARecommendation;
use App\Services\VirtualAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VAController extends Controller
{
    protected $vaService;

    public function __construct(VirtualAssistantService $vaService)
    {
        $this->vaService = $vaService;
    }

    /**
     * Generate recommendations for the user.
     */
    public function generateRecommendations(Request $request)
    {
        // Debug request info
        \Log::info('VA Generate Request', [
            'method' => $request->method(),
            'ajax' => $request->ajax(),
            'user' => Auth::check() ? Auth::id() : 'not-authenticated'
        ]);

        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Get user's schedules and transactions
        $schedules = Schedule::where('user_id', $user->id)->get();
        $transactions = Transaction::where('user_id', $user->id)->get();
        $target = MonthlyTarget::where('user_id', $user->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        try {
            // Generate recommendations using AI
            $recommendations = $this->vaService->generateRecommendations($schedules, $transactions, $target);

            // Store recommendations
            foreach ($recommendations as $rec) {
                VARecommendation::create([
                    'user_id' => $user->id,
                    'recommended_start' => $rec['start_time'],
                    'reason' => $rec['reason'],
                    'confidence_score' => $rec['confidence'],
                ]);
            }

            // Using Laravel's built-in notification system
            $user->notify(new \App\Notifications\NewRecommendations($recommendations));

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'recommendations' => $recommendations]);
            }

            return redirect()->back()->with('success', 'AI recommendations generated successfully.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Failed to generate recommendations: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to generate AI recommendations. Please try again.');
        }
    }

    /**
     * Display VA recommendations.
     */
    public function recommendations()
    {
        $recommendations = VARecommendation::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('va.recommendations', compact('recommendations'));
    }

    /**
     * Get VA recommendations for AJAX.
     */
    public function getRecommendationsAjax()
    {
        $recommendations = VARecommendation::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'recommended_start' => $rec->recommended_start->format('M d, Y H:i'),
                    'reason' => $rec->reason,
                    'confidence_score' => $rec->confidence_score,
                    'created_at' => $rec->created_at->diffForHumans(),
                ];
            });

        return response()->json($recommendations);
    }
}
