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
        Schema::create('followers_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_user_id')->nullable();
            $table->foreignId('followers_user_id')->nullable();
            $table->string('status_followers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers_profiles');
    }
};
