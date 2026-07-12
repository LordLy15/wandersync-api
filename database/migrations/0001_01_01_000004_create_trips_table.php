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
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('host_id');
            $table->foreign('host_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('destination');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_budget', 12, 2)->default(0);
            $table->string('share_code', 6)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
