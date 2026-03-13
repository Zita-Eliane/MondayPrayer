<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first()
            ?->markAsRead();

        return back();
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }
}
