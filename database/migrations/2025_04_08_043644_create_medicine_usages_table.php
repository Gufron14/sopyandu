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
        Schema::create('medicine_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('immunization_id')->nullable();
            $table->unsignedBigInteger('pregnancy_check_id')->nullable();
            $table->unsignedBigInteger('elderly_check_id')->nullable();
            $table->unsignedBigInteger('medicine_id')->nullable();
            $table->integer('quantity')->nullable(); // Jumlah obat yang diberikan
            $table->string('dosage_instructions')->nullable(); // Aturan pakai obat
            $table->enum('meal_time', ['-', 'Sebelum Makan', 'Sesudah Makan']); // Waktu makan (Sebelum Makan atau Sesudah Makan)
            $table->string('notes')->nullable(); // Catatan tambahan
            $table->timestamps();

            $table->foreign('immunization_id')->references('id')->on('immunizations')->onDelete('cascade');
            $table->foreign('pregnancy_check_id')->references('id')->on('pregnancy_checks')->onDelete('cascade');
            $table->foreign('elderly_check_id')->references('id')->on('elderly_checks')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_usages');
    }
};
