<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_name',
        'department',
        'item_id',
        'requested_quantity',
        'approved_quantity',
        'purpose',
        'status',
        'admin_note',
        'remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function requestItems()
    {
        return $this->hasMany(ItemRequestItem::class, 'item_request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getControlNumberAttribute()
    {
        return 'CN' . $this->created_at->format('mdY') . '-' . str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }
}
