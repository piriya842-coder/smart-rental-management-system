<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\LandlordApprovedMail;
use App\Mail\LandlordRejectedMail;

class LandlordApprovalController extends Controller
{
    /**
     * Pending landlords only (your existing page)
     */
    public function index()
    {
        $pendingLandlords = User::where('role', 'landlord')
            ->where('landlord_status', 'pending')
            ->latest()
            ->get();

        return view('admin.landlords.index', compact('pendingLandlords'));
    }

    /**
     * NEW: Show ALL landlords (Pending / Approved / Rejected)
     */
    public function all(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $q   = trim((string) $request->get('q', ''));

        $query = User::where('role', 'landlord');

        // Search
        if ($q !== '') {
            $query->where(function ($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Tab filter
        if ($tab !== 'all') {
            $query->where('landlord_status', $tab);
        }

        $landlords = $query->latest()->paginate(10)->withQueryString();

        // Counts
        $pendingCount  = User::where('role','landlord')
                            ->where('landlord_status','pending')->count();

        $approvedCount = User::where('role','landlord')
                            ->where('landlord_status','approved')->count();

        $rejectedCount = User::where('role','landlord')
                            ->where('landlord_status','rejected')->count();

        return view('admin.landlords.all', compact(
            'landlords',
            'tab',
            'q',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Approve landlord
     */
    public function approve($id)
    {
        $landlord = User::where('role', 'landlord')->findOrFail($id);

        $landlord->landlord_status = 'approved';
        $landlord->landlord_verified_at = now();
        $landlord->landlord_rejected_reason = null;
        $landlord->save();

        Mail::to($landlord->email)->send(new LandlordApprovedMail($landlord));

        return back()->with('success', 'Landlord approved and email sent.');
    }

    /**
     * Reject landlord
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $landlord = User::where('role', 'landlord')->findOrFail($id);

        $landlord->landlord_status = 'rejected';
        $landlord->landlord_verified_at = null;
        $landlord->landlord_rejected_reason = $request->reason;
        $landlord->save();

        Mail::to($landlord->email)->send(new LandlordRejectedMail($landlord));

        return back()->with('success', 'Landlord rejected and email sent.');
    }
}