<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('region');
            $table->decimal('harga', 15, 2);
            $table->float('luas_kamar');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->enum('tipe_kos', ['Campur', 'Pria', 'Wanita']);
            $table->boolean('is_km_dalam')->default(0);
            $table->boolean('is_water_heater')->default(0);
            $table->boolean('is_furnished')->default(0);
            $table->boolean('is_listrik_free')->default(0);
            $table->boolean('is_parkir_mobil')->default(0);
            $table->boolean('is_mesin_cuci')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
