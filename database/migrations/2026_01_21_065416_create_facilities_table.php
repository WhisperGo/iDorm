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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id(); // BigInt Unsigned - Penting untuk relasi bookings.facility_id
            $table->string('name', 50);
            $table->enum('type', ['heavy', 'light']);
            $table->text('description')->nullable();
            
            // Menggunakan standar Laravel agar lebih rapi
            $table->timestamps(); // Menggantikan created_at & updated_at manual
            $table->softDeletes(); // Menggantikan deleted_at manual
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};