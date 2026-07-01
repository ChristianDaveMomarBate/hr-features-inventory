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
        return [
            'request_id'     => $this->itemRequest->id,
            'requester_name' => $this->itemRequest->requester_name,
            'department'     => $this->itemRequest->department,
            'item_name'      => $this->itemRequest->item->name ?? 'Unknown Item',
            'quantity'       => $this->itemRequest->requested_quantity,
            'message'        => "{$this->itemRequest->requester_name} submitted a new item request.",
        ];
    }
}
