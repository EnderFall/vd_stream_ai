<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Handle AJAX requests for CRUD operations
        if ($request->expectsJson()) {
            return $this->handleAjaxRequest($request);
        }

        // Financial summary calculations
        $userId = Auth::id();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->sum('amount');

        $summary = [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
        ];

        $transactions = Transaction::where('user_id', $userId)
            ->with('schedule')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('financial.index', compact('transactions', 'summary'));
    }

    /**
     * Handle AJAX requests for CRUD operations
     */
    private function handleAjaxRequest(Request $request)
    {
        $action = $request->get('action');
        $id = $request->get('id');

        switch ($action) {
            case 'store':
                return $this->store($request);
            case 'update':
                return $this->update($request, $id);
            case 'destroy':
                return $this->destroy($id);
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schedules = Schedule::where('user_id', Auth::id())->get();
        return view('transactions.create', compact('schedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'transaction' => $transaction]);
        }

        return redirect()->route('financial.index')->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->with('schedule')->findOrFail($id);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $schedules = Schedule::where('user_id', Auth::id())->get();
        return view('transactions.edit', compact('transaction', 'schedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $transaction->update($request->only([
            'type', 'category', 'amount', 'date', 'description'
        ]));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'transaction' => $transaction]);
        }

        return redirect()->route('financial.index')->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $transaction->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('financial.index')->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Generate monthly report
     */
    public function monthlyReport($year, $month)
    {
        $user = Auth::user();

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('schedule')
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $incomeByCategory = $transactions->where('type', 'income')
            ->groupBy('category')
            ->map(function ($group) {
                return $group->sum('amount');
            });

        $expensesByCategory = $transactions->where('type', 'expense')
            ->groupBy('category')
            ->map(function ($group) {
                return $group->sum('amount');
            });

        return view('reports.monthly', compact(
            'year', 'month', 'transactions', 'totalIncome', 'totalExpenses',
            'netProfit', 'incomeByCategory', 'expensesByCategory'
        ));
    }

    /**
     * Export financial data to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'all');

        if ($format === 'monthly') {
            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->get('start_date', now()->startOfMonth()->format('Y-m-d')));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->get('end_date', now()->endOfMonth()->format('Y-m-d')));
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $pdf = Pdf::loadView('reports.financial_pdf', compact(
            'transactions', 'totalIncome', 'totalExpenses', 'netProfit', 'user', 'startDate', 'endDate'
        ));

        return $pdf->download("financial-report-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.pdf");
    }

    /**
     * Export financial data to CSV
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'all');

        if ($format === 'monthly') {
            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->get('start_date', now()->startOfMonth()->format('Y-m-d')));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->get('end_date', now()->endOfMonth()->format('Y-m-d')));
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $filename = "financial-report-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, ['Date', 'Type', 'Category', 'Amount', 'Description']);

            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->date->format('Y-m-d'),
                    $transaction->type,
                    $transaction->category,
                    $transaction->amount,
                    $transaction->description ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export financial data
     */
    public function export($format, Request $request)
    {
        if ($format === 'pdf') {
            return $this->exportPdf($request);
        } elseif ($format === 'csv') {
            return $this->exportCsv($request);
        }

        abort(404);
    }

    /**
     * Get transaction data for AJAX requests.
     */
    public function showAjax($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'id' => $transaction->id,
            'type' => $transaction->type,
            'category' => $transaction->category,
            'amount' => $transaction->amount,
            'date' => $transaction->date->format('Y-m-d'),
            'description' => $transaction->description,
        ]);
    }
}
