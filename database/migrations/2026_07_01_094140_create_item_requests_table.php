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
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name');
            $table->string('department');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->integer('requested_quantity')->default(1);
            $table->integer('approved_quantity')->nullable();
            $table->text('purpose')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Adjusted', 'Cancelled'])->default('Pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_requests');
    }
};
