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
        Schema::create('building_complaints', function (Blueprint $table) {
            $table->id();

            // Gunakan foreignId agar tipe data BigInt Unsigned (SINKRON!)
            // resident_id merujuk ke tabel users
            $table->foreignId('resident_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->string('location_item', 100);
            $table->text('description');

            // status_id merujuk ke tabel complaint_statuses
            $table->foreignId('status_id')
                  ->constrained('complaint_statuses')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->string('photo_path')->nullable();
            
            // Standar Laravel untuk waktu
            $table->timestamps();
            $table->softDeletes();
        });     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_complaints');
    }
};