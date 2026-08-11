<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyManage extends Model
{
    use HasFactory;

    protected $table = 'property_manage';

    protected $fillable = [
        'property_no',
        'item_description',
        'date_acquired',
        'unit_of_measurement',
        'quantity',
        'unit_value',
        'total_cost',
        'PAR_number',
        'remarks',
        'current_user',
        'status',
        'attachment',
    ];

    protected $casts = [
        'date_acquired' => 'datetime',
        'quantity'      => 'integer',
        'unit_value'    => 'decimal:2',
        'total_cost'    => 'decimal:2',
    ];
}