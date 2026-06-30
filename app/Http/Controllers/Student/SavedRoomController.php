<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class SavedRoomController extends Controller
{
    /**
     * Show saved rooms page
     */
    public function index()
    {
        $user = auth()->user();

        // ✅ Load saved rooms (NO facilities relationship!)
        $q = $user->savedRooms();

        // ✅ Only eager-load relationships that реально exist
        if (method_exists(Room::class, 'images')) {
            $q->with('images');
        }

        if (method_exists(Room::class, 'landlord')) {
            $q->with('landlord');
        }

        // ✅ Facilities is an ARRAY column in rooms table (cast), not a relation.
        // So DO NOT do: ->with('facilities')

        $rooms = $q->latest()->paginate(9);

        return view('student.saved.index', compact('rooms'));
    }

    /**
     * Toggle save/unsave
     */
    public function toggle(Request $request, $roomId)
    {
        $user = auth()->user();

        $room = Room::findOrFail($roomId);

        $exists = $user->savedRooms()
            ->where('rooms.id', $room->id)
            ->exists();

        if ($exists) {
            $user->savedRooms()->detach($room->id);
            $msg = 'Removed from Saved Rooms.';
        } else {
            $user->savedRooms()->attach($room->id);
            $msg = 'Saved to your favourites.';
        }

        // ✅ stay on same page + keep scroll near the card
        return redirect()->back()->with('success', $msg)->withFragment('room-' . $room->id);
    }
}
