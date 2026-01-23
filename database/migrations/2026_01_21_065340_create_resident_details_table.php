<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resident_details', function (Blueprint $table) {
            $table->id();

            // GABUNGKAN definisi kolom dan foreign key di sini
            // Pakai foreignId agar otomatis BigInt (sinkron dengan users.id)
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->string('full_name', 100);
            $table->enum('gender', ['Male', 'Female']);
            $table->string('class_name', 7);
            $table->string('room_number', 4);
            $table->string('phone_number', 15)->nullable();
            
            // Gunakan standar timestamps & softDeletes
            $table->timestamps();
            $table->softDeletes();
        });

        // Constraint CHECK untuk validasi format (Tetap dipertahankan)
        DB::statement("ALTER TABLE resident_details ADD CONSTRAINT chk_class_name CHECK (class_name REGEXP '^[A-Z]{4} [0-9]{2}$')");
        DB::statement("ALTER TABLE resident_details ADD CONSTRAINT chk_room_number CHECK (room_number REGEXP '^[A|B][0-9]{3}$')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_details');
    }
};