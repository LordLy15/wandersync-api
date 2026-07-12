<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripMember;
use App\Services\ShareCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function __construct(private ShareCodeService $shareCodeService) {}

    public function index()
    {
        $trips = Trip::whereHas('members', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('host')->get();

        return response()->json([
            'success' => true,
            'data' => $trips,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_budget' => 'required|numeric|min:0',
        ]);

        $trip = Trip::create([
            ...$validated,
            'host_id' => Auth::id(),
            'share_code' => $this->shareCodeService->generateUniqueCode(),
        ]);

        TripMember::create([
            'trip_id' => $trip->id,
            'user_id' => Auth::id(),
            'role' => 'host',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trip created successfully',
            'data' => $trip->load('host'),
        ], 201);
    }

    public function show(string $id)
    {
        $trip = Trip::with(['host', 'members.user', 'expenses', 'itineraryItems'])
            ->findOrFail($id);

        $this->authorizeMember($trip);

        return response()->json([
            'success' => true,
            'data' => $trip,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $trip = Trip::findOrFail($id);

        if ($trip->host_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only host can update trip',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'destination' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'total_budget' => 'sometimes|numeric|min:0',
        ]);

        $trip->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Trip updated successfully',
            'data' => $trip,
        ]);
    }

    public function destroy(string $id)
    {
        $trip = Trip::findOrFail($id);

        if ($trip->host_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only host can delete trip',
            ], 403);
        }

        $trip->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trip deleted successfully',
        ]);
    }

    public function join(Request $request)
    {
        $validated = $request->validate([
            'share_code' => 'required|string|size:6',
        ]);

        $trip = Trip::where('share_code', $validated['share_code'])->first();

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid share code',
            ], 404);
        }

        $existingMember = TripMember::where('trip_id', $trip->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($existingMember) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of this trip',
            ], 400);
        }

        TripMember::create([
            'trip_id' => $trip->id,
            'user_id' => Auth::id(),
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Joined trip successfully',
            'data' => $trip->load('host'),
        ]);
    }

    public function members(string $id)
    {
        $trip = Trip::findOrFail($id);
        $this->authorizeMember($trip);

        $members = $trip->members()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

    public function removeMember(string $id, string $userId)
    {
        $trip = Trip::findOrFail($id);

        if ($trip->host_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only host can remove members',
            ], 403);
        }

        if ($trip->host_id === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove host from trip',
            ], 400);
        }

        TripMember::where('trip_id', $id)
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member removed successfully',
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
