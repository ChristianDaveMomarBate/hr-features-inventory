<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_transfer', function (Blueprint $table) {
            $table->id();
            $table->uuid('transfer_no')->unique();
            $table->dateTime('transfer_date');
            $table->integer('items');
            $table->string('status')->default('Pending');
            $table->string('property_uuid');
            $table->string('curent_accountable_officer');
            $table->string('curent_accountable_officer_office');
            $table->string('transferto_accountable_officer');
            $table->string('transferto_accountable_officer_office');
            $table->text('transfer_remarks')->nullable();
            $table->json('transfer_attachment')->nullable();
            $table->string('transfer_approval_prepared_by');
            $table->dateTime('transfer_approval_prepared_by_date');
            $table->string('transfer_approval_approved_by');
            $table->dateTime('transfer_approval_approved_by_date');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_transfer');
    }
};
