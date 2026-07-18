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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        $data = $notification->data;
        $type = $notification->type;

        // Smart redirect based on notification type
        if ($type === 'App\Notifications\NewItemRequest') {
            // Go to Item Requests tab
            return redirect()->route('dashboard', ['page' => 'item-requests']);
        }

        if ($type === 'App\Notifications\LowStockAlert') {
            // Go to Inventory Registry tab, highlight the specific item
            $itemId = $data['inventory_item_id'] ?? null;
            return redirect()->route('dashboard', array_filter([
                'page'      => 'inventory-registry',
                'highlight' => $itemId,
            ]));
        }

        // Fallback
        return redirect()->route('dashboard', ['page' => $request->input('page', 'dashboard')]);
    }

    /**
     * Return unread notifications as JSON for real-time polling.
     */
    public function live()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notifications = $user->unreadNotifications()->latest()->take(15)->get();

        $items = $notifications->map(function ($n) {
            $data = $n->data;
            $isRequest = $n->type === 'App\Notifications\NewItemRequest';
            return [
                'id'         => $n->id,
                'type'       => $isRequest ? 'request' : 'low_stock',
                'title'      => $isRequest
                    ? ($data['requester_name'] ?? 'Someone') . ' submitted a request'
                    : ($data['name'] ?? 'Unknown Item'),
                'sub'        => $isRequest
                    ? ($data['department'] ?? '') . ' · Qty: ' . ($data['quantity'] ?? 0)
                    : 'Stock: ' . ($data['current_stock_label'] ?? $data['current_stock'] ?? 0) . ' — Min: ' . ($data['minimum_stock_label'] ?? $data['minimum_stock'] ?? 0),
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'count' => $notifications->count(),
            'items' => $items,
        ]);
    }
}
