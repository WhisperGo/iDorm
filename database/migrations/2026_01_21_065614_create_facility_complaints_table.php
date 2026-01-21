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
        Schema::create('facility_complaints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resident_id')->unsigned();
            $table->integer('booking_id')->unsigned();
            $table->text('description');
            $table->integer('status_id')->unsigned();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();
        
            $table->foreign('resident_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('complaint_statuses')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_complaints');
    }
};
