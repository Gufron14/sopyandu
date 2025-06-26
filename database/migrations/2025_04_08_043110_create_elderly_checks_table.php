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
        Schema::create('elderly_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('elderly_id'); // Relasi ke tabel lansia (anggota keluarga yang berstatus lansia)
            $table->date('check_date'); // Tanggal pemeriksaan
            $table->string('age_in_checks')->nullable(); // Usia lansia saat pemeriksaan, format: "65 tahun, 3 bulan, 12 hari"
            $table->float('body_weight')->nullable(); // Berat badan dalam kg
            $table->string('blood_pressure')->nullable(); // Tekanan darah, misal "130/85"
            $table->integer('pulse_rate')->nullable(); // Denyut nadi (bpm)
            $table->float('blood_sugar')->nullable(); // Gula darah sewaktu (mg/dL)
            $table->float('cholesterol')->nullable(); // Kadar kolesterol (mg/dL)
            $table->float('uric_acid')->nullable(); // Asam urat (mg/dL)
            $table->enum('mobility_status', ['Mandiri', 'Bantuan Alat', 'Dibantu Orang Lain'])->nullable(); // Status mobilitas lansia
            $table->enum('cognitive_status', ['Normal', 'Penurunan Ringan', 'Demensia'])->nullable(); // Status kognitif lansia
            $table->enum('nutritional_status', ['Baik', 'Kurang', 'Lebih'])->nullable(); // Status gizi lansia
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->unsignedBigInteger('officer_id')->nullable(); // ID petugas pemeriksa
            $table->timestamps();

            // Relasi ke tabel lansia (misalnya elderlies dengan tipe lansia atau tabel khusus lansia)
            $table->foreign('elderly_id')->references('id')->on('elderlies')->onDelete('cascade');
            // Relasi ke petugas medis
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elderly_checks');
    }
};
