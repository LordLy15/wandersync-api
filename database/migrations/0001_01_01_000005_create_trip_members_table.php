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
        Schema::create('trip_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trip_id');
            $table->uuid('user_id');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('role', ['host', 'member'])->default('member');
            $table->timestamp('joined_at');
            $table->unique(['trip_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_members');
    }
};
