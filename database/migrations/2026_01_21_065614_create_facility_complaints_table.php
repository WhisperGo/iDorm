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
        Schema::create('facility_complaints', function (Blueprint $table) {
            $table->id();

            // resident_id merujuk ke tabel users (BigInt Unsigned)
            $table->foreignId('resident_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // booking_id merujuk ke tabel bookings (BigInt Unsigned)
            $table->foreignId('booking_id')
                  ->constrained('bookings')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->text('description');

            // status_id merujuk ke tabel complaint_statuses (BigInt Unsigned)
            $table->foreignId('status_id')
                  ->constrained('complaint_statuses')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Gunakan standar Laravel untuk pengelolaan waktu
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_complaints');
    }
};