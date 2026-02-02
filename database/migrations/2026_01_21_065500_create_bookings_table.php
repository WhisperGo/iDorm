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

            // Relasi
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('facility_id')->constrained('facilities')->onUpdate('cascade')->onDelete('restrict');

            // HAPUS ->after('facility_id') di sini!
            $table->string('item_dapur')->nullable();
            $table->string('item_sergun')->nullable();

            $table->foreignId('status_id')->constrained('booking_statuses')->onUpdate('cascade')->onDelete('restrict');

            // Slot ID
            $table->foreignId('slot_id')
                    ->nullable() 
                    ->constrained('time_slots')
                    ->onDelete('set null');

            // Data Waktu
            $table->date('booking_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->text('description')->nullable();
            $table->integer('jumlah_orang')->nullable();

            // Data Kebersihan & Bukti
            $table->string('photo_proof_path')->nullable();
            $table->enum('cleanliness_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_feedback')->nullable();

            // Data Tambahan
            $table->boolean('is_early_release')->default(false);
            $table->dateTime('actual_finish_at')->nullable();

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