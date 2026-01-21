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
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('facility_id')->unsigned();
            $table->integer('slot_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->date('booking_date');
            $table->boolean('is_early_release')->default(false);
            $table->dateTime('actual_finish_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();
        
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('facility_id')->references('id')->on('facilities')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('slot_id')->references('id')->on('time_slots')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('booking_statuses')->onUpdate('cascade')->onDelete('restrict');
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
