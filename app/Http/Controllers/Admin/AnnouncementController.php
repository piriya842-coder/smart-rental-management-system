<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AnnouncementPublished;

class AnnouncementController extends Controller
{
    public function index()
    {
        // ✅ very important: paginate to avoid timeout
        $announcements = Announcement::query()
            ->latest()
            ->paginate(12);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:160'],
            'message' => ['required','string'],
            'is_active' => ['nullable'],
        ]);

        $announcement = Announcement::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        // ✅ send DB notification to all students + landlords
        // (keep chunking to avoid timeout)
        User::query()
            ->whereIn('role', ['student','landlord'])
            ->select('id') // ✅ small optimization (safe)
            ->chunkById(200, function ($users) use ($announcement) {
                Notification::send($users, new AnnouncementPublished($announcement));
            });

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement published and sent to users.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}