<?php

namespace App\View\Composers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * Automatically inject notification data into every view
     * that includes the navbar (so all dashboard pages get it).
     */
    public function compose(View $view): void
    {
        if (! Auth::check()) {
            $view->with('dashboardNotifications', collect());
            $view->with('unreadNotificationCount', 0);

            return;
        }

        /** @var User $user */
        $user = Auth::user();
        $dashboardNotifications = $user->unreadNotifications()->latest()->take(15)->get();
        $unreadNotificationCount = $dashboardNotifications->count();

        $view->with('dashboardNotifications', $dashboardNotifications);
        $view->with('unreadNotificationCount', $unreadNotificationCount);
    }
}
