<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_status', function (Blueprint $table) {
            $table->id();
            $table->string('status_code')->nullable();
            $table->string('status_name')->nullable();
            $table->timestamps();
        });

        DB::table('user_status')->insert([
            [
                'id' => 1,
                'status_code' => 101,
                'status_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'status_code' => 202,
                'status_name' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_status');
    }
};
