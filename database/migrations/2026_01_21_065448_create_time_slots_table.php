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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id(); // BigInt Unsigned - Penting agar sinkron dengan bookings.slot_id
            $table->enum('facilities', ['heavy', 'light']);
            $table->time('start_time');
            $table->time('end_time');
            
            // Gunakan standar Laravel
            $table->timestamps(); // Menghasilkan created_at & updated_at secara otomatis
            $table->softDeletes(); // Menghasilkan deleted_at yang defaultnya NULL (Penting!)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};