<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_request_id',
        'inventory_item_id',
        'requested_quantity',
        'approved_quantity',
        'remarks',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer',
    ];

    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
