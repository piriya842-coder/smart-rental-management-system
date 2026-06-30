<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ListingVerificationController extends Controller
{
    public function index(Request $request)
    {
        $tab = strtolower((string) $request->get('tab', 'pending'));
        if (!in_array($tab, ['pending','approved','rejected','all'], true)) {
            $tab = 'pending';
        }

        $q = trim((string) $request->get('q', ''));

        $query = Room::query();

        // ✅ eager load (if relationships exist)
        try { $query->with(['landlord', 'images']); } catch (\Throwable $e) {}

        // ✅ Search (SAFE columns only)
        if ($q !== '') {
            $query->where(function ($s) use ($q) {

                $s->where('title', 'like', "%{$q}%");

                if (Schema::hasColumn('rooms', 'address')) {
                    $s->orWhere('address', 'like', "%{$q}%");
                }
                if (Schema::hasColumn('rooms', 'city')) {
                    $s->orWhere('city', 'like', "%{$q}%");
                }
                if (Schema::hasColumn('rooms', 'state')) {
                    $s->orWhere('state', 'like', "%{$q}%");
                }
            });
        }

        if ($tab !== 'all') {
            $query->where('verification_status', $tab);
        }

        $rooms = $query->latest()->paginate(10)->withQueryString();

        // Counts (real)
        $pendingCount  = Room::where('verification_status', 'pending')->count();
        $approvedCount = Room::where('verification_status', 'approved')->count();
        $rejectedCount = Room::where('verification_status', 'rejected')->count();

        return view('admin.listings.verify', compact(
            'rooms',
            'tab',
            'q',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    public function approve(Room $room)
    {
        $room->verification_status = 'approved';
        $room->verification_reason = null;
        $room->verified_at = now();
        $room->save();

        return back()->with('success', 'Listing approved successfully. Students can now see it.');
    }

    public function reject(Request $request, Room $room)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $room->verification_status = 'rejected';
        $room->verification_reason = $request->reason;
        $room->verified_at = null;
        $room->save();

        return back()->with('success', 'Listing rejected successfully.');
    }
}