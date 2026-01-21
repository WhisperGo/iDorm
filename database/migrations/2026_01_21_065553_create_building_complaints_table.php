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
            $table->increments('id');
            $table->integer('resident_id')->unsigned();
            $table->string('location_item', 100);
            $table->text('description');
            $table->integer('status_id')->unsigned();
            $table->string('photo_path')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('resident_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('complaint_statuses')->onUpdate('cascade')->onDelete('restrict');
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
