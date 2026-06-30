<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Notifications\DisputeStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminDisputeController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string)$request->get('q', ''));
        $status   = trim((string)$request->get('status', 'all'));
        $priority = trim((string)$request->get('priority', 'all'));
        $category = trim((string)$request->get('category', 'all'));

        $query = Dispute::query()
            ->with(['student', 'landlord', 'booking', 'room'])
            ->latest('updated_at');

        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('code', 'like', "%{$q}%")
                  ->orWhere('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            })->orWhereHas('student', function ($u) use ($q) {
                $u->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            })->orWhereHas('landlord', function ($u) use ($q) {
                $u->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($status !== 'all' && $status !== '') {
            $query->where('status', $status);
        }

        if ($priority !== 'all' && $priority !== '') {
            $query->where('priority', $priority);
        }

        if ($category !== 'all' && $category !== '') {
            $query->where('category', $category);
        }

        $disputes = $query->paginate(10)->appends($request->query());

        $openCount     = Dispute::where('status', 'open')->count();
        $reviewCount   = Dispute::where('status', 'in_review')->count();
        $resolvedCount = Dispute::where('status', 'resolved')->count();
        $highCount     = Dispute::where('priority', 'high')->whereIn('status', ['open', 'in_review'])->count();

        return view('admin.disputes.index', compact(
            'disputes',
            'q',
            'status',
            'priority',
            'category',
            'openCount',
            'reviewCount',
            'resolvedCount',
            'highCount'
        ));
    }

    public function show(Dispute $dispute)
    {
        $dispute->load(['student', 'landlord', 'booking', 'room', 'resolver']);

        return view('admin.disputes.show', [
            'dispute' => $dispute,
        ]);
    }

    public function updateStatus(Request $request, Dispute $dispute)
    {
        $request->validate([
            'status' => 'required|in:open,in_review,resolved,rejected',
        ]);

        $dispute->status = $request->status;

        if (in_array($dispute->status, ['resolved', 'rejected'], true)) {
            $dispute->resolved_at = now();
            $dispute->resolved_by = Auth::id();
        } else {
            $dispute->resolved_at = null;
            $dispute->resolved_by = null;
        }

        $dispute->save();

        $this->notifySubmitter($dispute, 'status_updated');

        return back()->with('success', 'Ticket status updated.');
    }

    public function saveAdminNote(Request $request, Dispute $dispute)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:10000',
        ]);

        $dispute->admin_note = $request->admin_note;
        $dispute->save();

        return back()->with('success', 'Admin note saved.');
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        $request->validate([
            'resolution'      => 'required|string|max:100',
            'outcome_details' => 'nullable|string|max:10000',
        ]);

        $dispute->status = 'resolved';
        $dispute->resolution = $request->resolution;
        $dispute->outcome_details = $request->outcome_details;
        $dispute->resolved_at = now();
        $dispute->resolved_by = Auth::id();
        $dispute->save();

        $this->notifySubmitter($dispute, 'resolved');

        return back()->with('success', 'Ticket resolved successfully.');
    }

    public function reject(Request $request, Dispute $dispute)
    {
        $request->validate([
            'outcome_details' => 'nullable|string|max:10000',
        ]);

        $dispute->status = 'rejected';
        $dispute->resolution = 'rejected';
        $dispute->outcome_details = $request->outcome_details;
        $dispute->resolved_at = now();
        $dispute->resolved_by = Auth::id();
        $dispute->save();

        $this->notifySubmitter($dispute, 'rejected');

        return back()->with('success', 'Ticket rejected.');
    }

    public function uploadEvidence(Request $request, Dispute $dispute)
    {
        $request->validate([
            'evidence' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        if ($dispute->evidence_path && Storage::disk('public')->exists($dispute->evidence_path)) {
            Storage::disk('public')->delete($dispute->evidence_path);
        }

        $path = $request->file('evidence')->store('disputes', 'public');
        $dispute->evidence_path = $path;
        $dispute->save();

        return back()->with('success', 'Evidence uploaded.');
    }

    private function notifySubmitter(Dispute $dispute, string $eventType): void
    {
        $dispute->loadMissing('submitter');

        if (!$dispute->submitter) {
            return;
        }

        $dispute->submitter->notify(new DisputeStatusUpdated($dispute, $eventType));
    }
}