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
        Schema::create('reward_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')->nullable()->constrained('rewards')->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->string('image_name')->nullable();
            $table->string('image_url')->nullable();
            $table->binary('image_data')->type('LONGBLOB')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_images');
    }
};
