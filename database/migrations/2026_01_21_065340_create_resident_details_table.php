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
            $table->increments('id');
            $table->integer('user_id')->unsigned()->unique();
            $table->string('full_name', 100);
            $table->enum('gender', ['Male', 'Female']);
            $table->string('class_name', 7);
            $table->string('room_number', 4);
            $table->string('phone_number', 15)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

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
