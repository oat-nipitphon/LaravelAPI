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
        Schema::create('user_points_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('reward_id');
            $table->foreignId('point_status');
            $table->text('detail_counter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_points_counters');
    }
};
