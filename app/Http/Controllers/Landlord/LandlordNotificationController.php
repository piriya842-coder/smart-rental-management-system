<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandlordNotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $notifications = $user->notifications()->latest()->paginate(10);
        $unreadCount   = $user->unreadNotifications()->count();

        return view('landlord.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function read(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        if (count($ids)) {
            auth()->user()
                ->unreadNotifications()
                ->whereIn('id', $ids)
                ->update(['read_at' => now()]);
        }

        return back()->with('success', 'Selected notifications marked as read.');
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function deleteSelected(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        if (count($ids)) {
            auth()->user()->notifications()->whereIn('id', $ids)->delete();
        }

        return back()->with('success', 'Selected notifications deleted.');
    }

    public function clearAll()
    {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'All notifications cleared.');
    }
}