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
        'stock_unit',
        'issue_unit',
        'units_per_stock_unit',
        'stock',
        'minimum',
        'condition',
        'description',
        'location',
        'date_registered'
    ];

    protected $casts = [
        'stock'   => 'integer',
        'minimum' => 'integer',
        'units_per_stock_unit' => 'integer',
    ];

    protected $appends = [
        'display_stock',
        'bulk_equivalent',
        'display_unit',
    ];

    public function getDisplayUnitAttribute(): string
    {
        return $this->issue_unit ?: ($this->unit ?: 'pcs');
    }

    public function getDisplayStockAttribute(): string
    {
        return number_format((int) $this->stock) . ' ' . $this->display_unit;
    }

    public function getBulkEquivalentAttribute(): ?string
    {
        $stockUnit = $this->stock_unit ?: $this->display_unit;
        $unitsPerStockUnit = max((int) ($this->units_per_stock_unit ?: 1), 1);

        if ($stockUnit === $this->display_unit || $unitsPerStockUnit <= 1) {
            return null;
        }

        $bulkQty = (int) floor(((int) $this->stock) / $unitsPerStockUnit);
        $remainder = ((int) $this->stock) % $unitsPerStockUnit;

        if ($bulkQty < 1) {
            return 'less than 1 ' . $stockUnit;
        }

        $text = 'about ' . number_format($bulkQty) . ' ' . $this->pluralizeUnit($stockUnit, $bulkQty);
        if ($remainder > 0) {
            $text .= ' + ' . number_format($remainder) . ' ' . $this->display_unit;
        }

        return $text;
    }

    private function pluralizeUnit(string $unit, int $quantity): string
    {
        if ($quantity === 1 || in_array($unit, ['pcs', 'sheets'], true)) {
            return $unit;
        }

        return match ($unit) {
            'box' => 'boxes',
            default => $unit . 's',
        };
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

}
