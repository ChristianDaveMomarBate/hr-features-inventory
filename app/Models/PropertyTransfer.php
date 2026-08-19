<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyTransfer extends Model
{
   use HasFactory;
    protected $table = 'property_transfer';
    protected $fillable = [
        'transfer_no',
        'transfer_date',
        'items',
        'status',
        'property_uuid',
        'curent_accountable_officer',
        'curent_accountable_officer_office',
        'transferto_accountable_officer',
        'transferto_accountable_officer_office',
        'transfer_remarks',
        'transfer_attachment',
        'transfer_approval_prepared_by',
        'transfer_approval_prepared_by_date',
        'transfer_approval_approved_by',
        'transfer_approval_approved_by_date',
    ];

    protected $casts = [
        'transfer_date' => 'datetime',
        'items' => 'integer',
        'property_uuid' => 'string',
        'transfer_attachment' => 'array',
        'transfer_approval_prepared_by_date' => 'datetime',
        'transfer_approval_approved_by_date' => 'datetime',
    ];
}
