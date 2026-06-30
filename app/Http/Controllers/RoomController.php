<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::where('landlord_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('landlord.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $facilityOptions = $this->facilityOptions();
        return view('landlord.rooms.create', compact('facilityOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRoom($request);

        // checkbox facilities => array
        $data['facilities'] = $request->input('facilities', []);
        $data['landlord_id'] = $request->user()->id;

        // ✅ Capacity + available slots logic
        $roomType = strtolower((string)($data['room_type'] ?? 'single'));

        if ($roomType === 'shared') {
            // landlord can set capacity (default 4)
            $cap = (int)($request->input('capacity') ?? $request->input('available_slots') ?? 4);
            if ($cap < 1) $cap = 4;

            $data['capacity'] = $cap;
            $data['available_slots'] = $cap; // new room => full slots
            $data['is_available'] = true;
        } else {
            // single/studio => always 1 slot
            $data['capacity'] = 1;
            $data['available_slots'] = 1;
            $data['is_available'] = $request->boolean('is_available', true);
        }

        Room::create($data);

        return redirect()->route('landlord.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function edit(Request $request, Room $room)
    {
        $this->ensureOwner($request, $room);

        $facilityOptions = $this->facilityOptions();
        return view('landlord.rooms.edit', compact('room', 'facilityOptions'));
    }

    public function update(Request $request, Room $room)
    {
        $this->ensureOwner($request, $room);

        $data = $this->validateRoom($request);
        $data['facilities'] = $request->input('facilities', []);

        // ✅ Capacity + available slots logic on update
        $roomType = strtolower((string)($data['room_type'] ?? $room->room_type ?? 'single'));

        if ($roomType === 'shared') {
            $cap = (int)($request->input('capacity') ?? $request->input('available_slots') ?? $room->capacity ?? 4);
            if ($cap < 1) $cap = 4;

            // If bookings are empty (your case), safe to reset available_slots to capacity
            // For real system later, we would compute "available_slots = capacity - activeBookings"
            $data['capacity'] = $cap;

            // If available_slots missing or bigger than capacity, fix it
            $currentAvail = (int)($room->available_slots ?? 0);
            if ($currentAvail <= 0 || $currentAvail > $cap) {
                $data['available_slots'] = $cap;
            }

            $data['is_available'] = ($data['available_slots'] ?? $currentAvail) > 0;
        } else {
            $data['capacity'] = 1;
            $data['available_slots'] = 1;
            $data['is_available'] = $request->boolean('is_available', true);
        }

        $room->update($data);

        return redirect()->route('landlord.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Request $request, Room $room)
    {
        $this->ensureOwner($request, $room);

        $room->delete();

        return redirect()->route('landlord.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    private function ensureOwner(Request $request, Room $room): void
    {
        if ($room->landlord_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
    }

    private function validateRoom(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postcode' => ['nullable', 'string', 'max:20'],

            'price_monthly' => ['required', 'numeric', 'min:0'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:999'],

            'room_type' => ['required', 'in:single,shared,studio'],
            'gender_preference' => ['required', 'in:any,male,female'],

            // ✅ IMPORTANT: these exist in your DB
            'capacity' => ['nullable', 'integer', 'min:1', 'max:20'],
            'available_slots' => ['nullable', 'integer', 'min:0', 'max:20'],

            'is_available' => ['nullable'], // checkbox
        ]) + [
            'is_available' => $request->boolean('is_available'),
        ];
    }

    private function facilityOptions(): array
    {
        return [
            'wifi' => 'WiFi',
            'aircond' => 'Air Conditioner',
            'parking' => 'Parking',
            'laundry' => 'Laundry',
            'furnished' => 'Furnished',
            'near_bus' => 'Near Public Transport',
            'security' => 'Security',
            'water_included' => 'Water Included',
            'electric_included' => 'Electric Included',
        ];
    }
}
