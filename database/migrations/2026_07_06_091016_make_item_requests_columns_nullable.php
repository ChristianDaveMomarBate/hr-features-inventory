<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE item_requests MODIFY item_id bigint unsigned NULL');
        DB::statement('ALTER TABLE item_requests MODIFY requested_quantity int NULL');
        DB::statement('ALTER TABLE item_requests MODIFY approved_quantity int NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE item_requests MODIFY item_id bigint unsigned NOT NULL');
        DB::statement('ALTER TABLE item_requests MODIFY requested_quantity int NOT NULL DEFAULT 1');
        // approved_quantity was already nullable in original migration, wait...
        // Let's check original migration: $table->integer('approved_quantity')->nullable();
        // So approved_quantity is already nullable! No need to modify it.
    }
};
