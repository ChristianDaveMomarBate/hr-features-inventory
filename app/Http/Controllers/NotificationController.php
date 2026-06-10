<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()->route('dashboard', ['page' => $request->input('page', 'dashboard')]);
    }
}
