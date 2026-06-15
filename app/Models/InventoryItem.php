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
        'category_id',
        'type',
        'unit',
        'stock',
        'minimum',
        'condition',
        'description',
        'location',
        'location_id',
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

    public function categoryModel()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function locationModel()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function assetIssuances()
    {
        return $this->hasMany(AssetIssuance::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
