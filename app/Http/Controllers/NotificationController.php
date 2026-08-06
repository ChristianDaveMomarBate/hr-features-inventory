<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Notifications\NewItemRequest;
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
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        $data = $notification->data;

        return match ($notification->type) {

            NewItemRequest::class => redirect()->route('dashboard', [
                'page' => 'item-requests',
            ]),

            LowStockAlert::class => redirect()->route('dashboard', array_filter([
                'page' => 'inventory-registry',
                'highlight' => $data['inventory_item_id'] ?? null,
            ])),

            default => redirect()->route('dashboard', [
                'page' => $request->input('page', 'dashboard'),
            ]),
        };
    }

    /**
     * Return unread notifications as JSON for real-time polling.
     */
    public function live()
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(15)
            ->get();

        $items = $notifications->map(function ($notification) {

            $data = $notification->data;

            $isRequest = $notification->type === NewItemRequest::class;

            return [
                'id' => $notification->id,
                'type' => $isRequest ? 'request' : 'low_stock',
                'title' => $isRequest
                    ? ($data['requester_name'] ?? 'Someone').' submitted a request'
                    : ($data['name'] ?? 'Unknown Item'),
                'sub' => $isRequest
                    ? ($data['department'] ?? '').' · Qty: '.($data['quantity'] ?? 0)
                    : 'Stock: '.($data['current_stock_label'] ?? $data['current_stock'] ?? 0)
                        .' — Min: '
                        .($data['minimum_stock_label'] ?? $data['minimum_stock'] ?? 0),
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'count' => $notifications->count(),
            'items' => $items,
        ]);
    }
}
