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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // 1. Relasi - Menggunakan foreignId agar BigInt Unsigned (SINKRON!)
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('facility_id')->constrained('facilities')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('status_id')->constrained('booking_statuses')->onUpdate('cascade')->onDelete('restrict');
            
            // 2. Slot ID (Boleh Kosong)
            $table->foreignId('slot_id')
                  ->nullable() 
                  ->constrained('time_slots')
                  ->onDelete('set null');

            // 3. Data Waktu (KOLOM INI WAJIB ADA karena dipanggil di Controller)
            $table->date('booking_date');
            $table->time('start_time')->nullable(); // Ditambahkan
            $table->time('end_time')->nullable();   // Ditambahkan

            // 4. Data Kebersihan & Bukti
            $table->string('photo_proof_path')->nullable();
            $table->enum('cleanliness_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_feedback')->nullable();

            // 5. Data Tambahan
            $table->boolean('is_early_release')->default(false);
            $table->dateTime('actual_finish_at')->nullable();
            
            // 6. Standar Laravel
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};