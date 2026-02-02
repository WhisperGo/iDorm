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
        Schema::create('manager_details', function (Blueprint $table) {
            $table->id();

            // Gunakan foreignId agar otomatis BIGINT UNSIGNED (sinkron dengan users.id)
            // Tambahkan unique() karena satu user hanya punya satu detail manager
            $table->foreignId('user_id')
                    ->unique()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->string('full_name', 100);
            $table->enum('gender', ['Male', 'Female']);
            $table->string('phone_number', 15)->nullable();
            $table->string('photo_path')->nullable();
            
            // Gunakan standar Laravel untuk timestamps dan softDeletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_details');
    }
};