<?php

namespace App\Notifications;

use App\Models\ItemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewItemRequest extends Notification
{
    use Queueable;

    public function __construct(private ItemRequest $itemRequest)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $itemsCount = $this->itemRequest->requestItems()->count();
        $totalQuantity = $this->itemRequest->requestItems()->sum('requested_quantity');

        $itemName = 'Unknown Item';
        if ($itemsCount === 1) {
            $firstItem = $this->itemRequest->requestItems()->first();
            $itemName = $firstItem && $firstItem->item ? $firstItem->item->name : 'Unknown Item';
        } elseif ($itemsCount > 1) {
            $itemName = $itemsCount . ' Items';
        }

        return [
            'request_id'     => $this->itemRequest->id,
            'requester_name' => $this->itemRequest->requester_name,
            'department'     => $this->itemRequest->department,
            'item_name'      => $itemName,
            'quantity'       => $totalQuantity,
            'message'        => "{$this->itemRequest->requester_name} submitted a new item request.",
        ];
    }
}
