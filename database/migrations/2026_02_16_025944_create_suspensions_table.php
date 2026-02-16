<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suspensions', function (Blueprint $table) {
            $table->id();

            // Siapa yang kena suspend
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Fasilitas apa?
            // NULL = Global Suspend (Otoritas Pengelola/Manager)
            // Terisi = Local Suspend (Otoritas Admin Fasilitas)
            $table->foreignId('facility_id')
                ->nullable()
                ->constrained('facilities')
                ->onDelete('cascade');

            // Siapa yang memberikan hukuman (Bisa Admin atau Pengelola)
            $table->foreignId('issued_by')
                ->constrained('users')
                ->onDelete('restrict');

            $table->text('reason');

            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable(); // Nullable jika hukuman permanen/belum ditentukan

            $table->timestamps();
            $table->softDeletes(); // Penting untuk audit trail jika hukuman dicabut sebelum waktunya

            // Indexing agar query pengecekan status cepat
            $table->index(['user_id', 'facility_id', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suspensions');
    }
};