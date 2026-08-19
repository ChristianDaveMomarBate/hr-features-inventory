<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     function up()
    {
        Schema::create('property_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('transfer_uuids');
            $table->unsignedBigInteger('property_transfer_id');
            $table->string('property_no');
            $table->text('item_description');
            $table->string('par_ics');
            $table->decimal('quantity', 12, 2);
            $table->string('unit_of_measurement');
            $table->decimal('unit_value', 12, 2);
            $table->string('condition');
            $table->string('date_acquired');
            $table->string('total_cost');
            $table->timestamps();
            $table->index('property_no');
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_transfer_items');
    }
};
