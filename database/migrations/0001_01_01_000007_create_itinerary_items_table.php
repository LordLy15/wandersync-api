<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trip_id');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->decimal('estimated_budget', 12, 2)->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_items');
    }
};
