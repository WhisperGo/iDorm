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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Gunakan foreignId agar sinkron dengan tipe BigInt di tabel users
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            $table->string('action', 50);
            $table->string('subject_type', 50)->nullable();
            
            // subject_id sebaiknya menggunakan unsignedBigInteger agar bisa menampung 
            // ID dari tabel lain yang sudah kita ubah ke BigInt
            $table->unsignedBigInteger('subject_id')->nullable();
            
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Untuk logs biasanya hanya butuh created_at
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};