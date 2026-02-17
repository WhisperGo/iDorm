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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary Key (BigInt Unsigned)

            // Perbaikan: Gunakan foreignId agar otomatis sinkron dengan tabel roles
            $table->foreignId('role_id')->constrained('roles')->onUpdate('cascade')->onDelete('restrict');
            // $table->foreignId('facility_id')->nullable()->constrained('facilities')->onDelete('set null');

            $table->string('card_id', 4)->unique();
            $table->string('password');
            $table->enum('account_status', ['active', 'frozen'])->default('active');
            $table->dateTime('last_login_at')->nullable();
            
            // Gunakan standar Laravel untuk timestamps dan softDeletes
            $table->timestamps(); // Menghasilkan created_at & updated_at
            $table->softDeletes(); // Menghasilkan deleted_at

            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};