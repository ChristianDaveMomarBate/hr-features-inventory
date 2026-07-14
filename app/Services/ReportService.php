<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReportService
{
    /**
     * Generate a JSON file containing all inventory and transaction data
     * optimized for generating the monthly report.
     */
    public static function generateMonthlyReportJson()
    {
        try {
            $items = InventoryItem::all();
            $transactions = StockTransaction::with('inventoryItem')->orderBy('created_at', 'asc')->get();

            $data = [
                'generated_at' => now()->toDateTimeString(),
                'items' => $items->toArray(),
                'transactions' => $transactions->toArray(),
            ];

            // Ensure the reports directory exists
            if (!Storage::disk('public')->exists('reports')) {
                Storage::disk('public')->makeDirectory('reports');
            }

            Storage::disk('public')->put('reports/monthly_report.json', json_encode($data, JSON_PRETTY_PRINT));
            Log::info("Monthly report JSON generated successfully.");
        } catch (\Exception $e) {
            Log::error("Failed to generate monthly report JSON: " . $e->getMessage());
        }
    }
}
