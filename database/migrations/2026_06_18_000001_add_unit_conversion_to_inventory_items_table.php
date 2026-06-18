<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('stock_unit')->default('pcs')->after('unit');
            $table->string('issue_unit')->default('pcs')->after('stock_unit');
            $table->unsignedInteger('units_per_stock_unit')->default(1)->after('issue_unit');
        });

        DB::table('inventory_items')->update([
            'stock_unit' => DB::raw('unit'),
            'issue_unit' => DB::raw('unit'),
            'units_per_stock_unit' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['stock_unit', 'issue_unit', 'units_per_stock_unit']);
        });
    }
};
