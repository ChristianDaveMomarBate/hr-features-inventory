<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTransferItem extends Model
{
    use HasFactory;
    protected $table = 'property_transfer_items';
    protected $fillable = [
        'transfer_uuids',
        'property_transfer_id',
        'property_no',
        'item_description',
        'par_ics',
        'quantity',
        'unit_of_measurement',
        'unit_value',
        'condition',
        'date_acquired',
        'total_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_value' => 'decimal:2',
    ];

    public function propertyTransfer(): BelongsTo
    {
        return $this->belongsTo(PropertyTransfer::class);
    }
}