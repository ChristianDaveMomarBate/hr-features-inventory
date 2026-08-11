<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('property_manage', function (Blueprint $table) {

            $table->id();
            $table->string('property_no');
            $table->string('item_description');
            $table->dateTime('date_acquired');
            $table->string('unit_of_measurement');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_value', 12, 2);
            $table->decimal('total_cost', 15, 2);
            $table->string('PAR_number');
            $table->text('remarks')->nullable();
            $table->string('current_user');
            $table->string('status');
            $table->string('attachment')->nullable();
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_manage');
    }
};
