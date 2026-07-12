<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Trip;
use App\Services\BudgetSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function __construct(private BudgetSummaryService $budgetSummaryService) {}

    public function index(string $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $this->authorizeMember($trip);

        $expenses = Expense::where('trip_id', $tripId)
            ->with('user')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    public function store(Request $request, string $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $this->authorizeMember($trip);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:transport,accommodation,tickets,food,others',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $expense = Expense::create([
            ...$validated,
            'trip_id' => $tripId,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully',
            'data' => $expense->load('user'),
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only expense owner can update',
            ], 403);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'category' => 'sometimes|in:transport,accommodation,tickets,food,others',
            'description' => 'nullable|string|max:255',
            'date' => 'sometimes|date',
        ]);

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data' => $expense->load('user'),
        ]);
    }

    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only expense owner can delete',
            ], 403);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
        ]);
    }

    public function summary(string $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $this->authorizeMember($trip);

        $summary = $this->budgetSummaryService->getSummary($tripId);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    private function authorizeMember(Trip $trip): void
    {
        $isMember = $trip->members()->where('user_id', Auth::id())->exists();

        if (!$isMember) {
            abort(response()->json([
                'success' => false,
                'message' => 'You are not a member of this trip',
            ], 403));
        }
    }
}
