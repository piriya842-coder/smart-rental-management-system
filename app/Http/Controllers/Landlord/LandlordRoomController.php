<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LandlordRoomController extends Controller
{
    public function index()
    {
        $landlordId = Auth::id();

        // IMPORTANT: paginate() so Blade can use $rooms->total(), $rooms->links()
        $rooms = Room::where('landlord_id', $landlordId)
            ->latest()
            ->paginate(10);

        return view('landlord.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('landlord.rooms.create');
    }

    public function store(Request $request)
{
    // Detect which button clicked
    $action = strtolower((string) $request->input('action', 'draft'));
    if (!in_array($action, ['draft', 'publish'], true)) {
        $action = 'draft';
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'price_monthly' => 'required|numeric|min:0',
        'distance_km' => 'nullable|numeric|min:0',
        'description' => 'nullable|string',

        'address' => 'nullable|string|max:255',
        'city' => 'required|string|max:120',
        'state' => 'required|string|max:120',
        'postcode' => 'required|string|max:20',

        'room_type' => 'required|string|max:50',
        'capacity' => 'nullable|integer|min:1|max:10',

        'gender_preference' => 'required|string|max:50',

        'facilities' => 'nullable|array',
        'facilities.*' => 'string|max:50',

        'is_available' => 'nullable|boolean',

        'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $room = new Room();
    $room->landlord_id = Auth::id();

    // Set status based on button
    $room->status = ($action === 'publish') ? 'active' : 'draft';

    $room->title = $validated['title'];
    $room->price_monthly = $validated['price_monthly'];
    $room->distance_km = $validated['distance_km'] ?? null;
    $room->description = $validated['description'] ?? null;

    $room->address = $validated['address'] ?? null;
    $room->city = $validated['city'];
    $room->state = $validated['state'];
    $room->postcode = $validated['postcode'];

    $room->room_type = $validated['room_type'];
    $room->gender_preference = $validated['gender_preference'];

    $room->facilities = isset($validated['facilities'])
        ? json_encode($validated['facilities'])
        : json_encode([]);

    // Capacity logic
    $type = strtolower((string) $room->room_type);
    $isShared = str_contains($type, 'shared');

    if ($isShared) {
        $cap = isset($validated['capacity']) ? (int)$validated['capacity'] : 4;
        if ($cap < 2) $cap = 2;

        $room->capacity = $cap;
        $room->available_slots = $cap;
        $room->is_available = true;
    } else {
        $room->capacity = 1;
        $room->available_slots = 1;
        $room->is_available = $request->boolean('is_available');
    }

    if ($request->hasFile('cover_image')) {
        $room->cover_image = $request->file('cover_image')
            ->store('rooms/covers', 'public');
    }

    // If verification exists, mark new rooms pending
    if (\Illuminate\Support\Facades\Schema::hasColumn('rooms', 'verification_status')) {
        $room->verification_status = 'pending';
        $room->verification_reason = null;
        $room->verified_at = null;
    }

    $room->save();

    return redirect()
        ->route('landlord.rooms.index')
        ->with('success', $action === 'publish'
            ? 'Room published successfully.'
            : 'Room saved as draft.');
}

    public function edit(Room $room)
    {
        $this->authorizeOwner($room);

        return view('landlord.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $this->authorizeOwner($room);

        $validated = $request->validate([
            'status' => 'nullable|in:draft,active,inactive',

            'title' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'distance_km' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',

            'address' => 'nullable|string|max:255',
            'city' => 'required|string|max:120',
            'state' => 'required|string|max:120',
            'postcode' => 'required|string|max:20',

            'room_type' => 'required|string|max:50',
            'gender_preference' => 'required|string|max:50',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:50',

            'is_available' => 'nullable|boolean',

            // ONE image only
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            // optional delete image checkbox
            'remove_cover_image' => 'nullable|boolean',
        ]);

        $room->status = $validated['status'] ?? $room->status;

        $room->title = $validated['title'];
        $room->price_monthly = $validated['price_monthly'];
        $room->distance_km = $validated['distance_km'] ?? null;
        $room->description = $validated['description'] ?? null;

        $room->address = $validated['address'] ?? null;
        $room->city = $validated['city'];
        $room->state = $validated['state'];
        $room->postcode = $validated['postcode'];

        $room->room_type = $validated['room_type'];
        $room->gender_preference = $validated['gender_preference'];

        $room->facilities = isset($validated['facilities']) ? json_encode($validated['facilities']) : json_encode([]);

        $room->is_available = $request->boolean('is_available');

        // delete image
        if ($request->boolean('remove_cover_image') && $room->cover_image) {
            Storage::disk('public')->delete($room->cover_image);
            $room->cover_image = null;
        }

        // upload new image
        if ($request->hasFile('cover_image')) {
            if ($room->cover_image) {
                Storage::disk('public')->delete($room->cover_image);
            }
            $room->cover_image = $request->file('cover_image')->store('rooms/covers', 'public');
        }

        $room->save();

        return redirect()->route('landlord.rooms.index')->with('success', 'Room updated successfully.');
    }

    public function publish(Room $room)
    {
        $this->authorizeOwner($room);

        $room->status = 'active';
        $room->save();

        return back()->with('success', 'Room published (Active).');
    }

    public function unpublish(Room $room)
    {
        $this->authorizeOwner($room);

        $room->status = 'draft';
        $room->save();

        return back()->with('success', 'Room moved to Draft.');
    }

    public function destroy(Room $room)
    {
        $this->authorizeOwner($room);

        if ($room->cover_image) {
            Storage::disk('public')->delete($room->cover_image);
        }

        $room->delete();

        return back()->with('success', 'Room deleted.');
    }

    private function authorizeOwner(Room $room): void
    {
        if ((int)$room->landlord_id !== (int)Auth::id()) {
            abort(403);
        }
    }
}
