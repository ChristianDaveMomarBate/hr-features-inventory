<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class InventoryItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'category',
        'type',
        'unit',
        'stock',
        'minimum',
        'description',
        'location',
        'date_registered'
    ];

    protected $casts = [
        'stock'   => 'integer',
        'minimum' => 'integer',
    ];

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }
}
