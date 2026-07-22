<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('admin_note');
        });

        Schema::table('item_request_items', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('approved_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });

        Schema::table('item_request_items', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
