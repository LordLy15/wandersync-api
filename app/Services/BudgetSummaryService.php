<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Trip;

class BudgetSummaryService
{
    public function getSummary(string $tripId): array
    {
        $trip = Trip::findOrFail($tripId);

        $expenses = Expense::where('trip_id', $tripId)->get();

        $totalSpent = $expenses->sum('amount');
        $remaining = $trip->total_budget - $totalSpent;
        $percentage = $trip->total_budget > 0
            ? round(($totalSpent / $trip->total_budget) * 100, 1)
            : 0;

        $byCategory = $expenses->groupBy('category')->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        });

        $status = 'under_budget';
        if ($totalSpent > $trip->total_budget) {
            $status = 'over_budget';
        } elseif ($percentage >= 80) {
            $status = 'warning';
        }

        return [
            'total_budget' => (float) $trip->total_budget,
            'total_spent' => (float) $totalSpent,
            'remaining' => (float) $remaining,
            'percentage' => $percentage,
            'status' => $status,
            'by_category' => $byCategory,
        ];
    }
}
