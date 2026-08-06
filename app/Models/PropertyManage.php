<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyManage extends Model
{
     use HasFactory;

    protected $fillable = [
        'id',
        'property_no',
        'item_description',
        'date_aqcuired',
        'unitOf_measurement',
        'quantity',
        'unit_value',
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
