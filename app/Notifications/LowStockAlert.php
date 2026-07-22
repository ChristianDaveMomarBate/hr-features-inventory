<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    public function __construct(private InventoryItem $item)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'inventory_item_id' => $this->item->id,
            'code' => $this->item->code,
            'name' => $this->item->name,
            'current_stock' => $this->item->stock,
            'current_stock_label' => $this->item->display_stock,
            'minimum_stock' => $this->item->minimum,
            'minimum_stock_label' => number_format((int) $this->item->minimum) . ' ' . $this->item->display_unit,
            'message' => "{$this->item->name} is low on stock.",
        ];
    }
}
