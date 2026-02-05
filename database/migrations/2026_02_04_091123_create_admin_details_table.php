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
        Schema::create('admin_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                    ->unique()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            
            $table->foreignId('facility_id')
                    ->unique()
                    ->constrained('facilities')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->string('full_name', 100);
            $table->enum('gender', ['Male', 'Female']);
            $table->string('class_name', 7);
            $table->string('room_number', 4);
            $table->string('phone_number', 15)->nullable();
            $table->string('photo_path')->nullable();
            
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_details');
    }
};
