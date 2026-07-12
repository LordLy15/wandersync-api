<?php

namespace App\Http\Controllers;

use App\Models\ItineraryItem;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItineraryController extends Controller
{
    public function index(string $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $this->authorizeMember($trip);

        $items = ItineraryItem::where('trip_id', $tripId)
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request, string $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $this->authorizeMember($trip);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
        ]);

        $maxOrder = ItineraryItem::where('trip_id', $tripId)->max('order_index') ?? -1;

        $item = ItineraryItem::create([
            ...$validated,
            'trip_id' => $tripId,
            'order_index' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Itinerary item added successfully',
            'data' => $item,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $item = ItineraryItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Itinerary item updated successfully',
            'data' => $item,
        ]);
    }

    public function destroy(string $id)
    {
        $item = ItineraryItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Itinerary item deleted successfully',
        ]);
    }

    public function reorder(Request $request, string $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $this->authorizeMember($trip);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'uuid|exists:itinerary_items,id',
        ]);

        foreach ($validated['items'] as $index => $itemId) {
            ItineraryItem::where('id', $itemId)
                ->where('trip_id', $tripId)
                ->update(['order_index' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Itinerary reordered successfully',
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
