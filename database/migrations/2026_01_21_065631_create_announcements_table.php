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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            // Gunakan foreignId agar otomatis BigInt Unsigned (SINKRON dengan users.id)
            $table->foreignId('author_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->string('title', 150);
            $table->text('content');

            // Standar Laravel untuk pengelolaan waktu
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};