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
        Schema::create('weighings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('children_id'); // Relasi ke tabel anak yang menjalani penimbangan
            $table->date('weighing_date')->nullable(); // Tanggal penimbangan, opsional
            $table->string('age_in_checks')->nullable(); // Usia anak saat penimbangan, misalnya dalam format "1 tahun, 1 bulan, 10 hari"
            $table->float('weight')->nullable();  // Berat badan anak dalam satuan kilogram (kg)
            $table->float('height')->nullable();  // Tinggi badan anak dalam satuan sentimeter (cm)
            $table->float('head_circumference')->nullable();  // Lingkar kepala anak dalam satuan sentimeter (cm)
            $table->float('arm_circumference')->nullable();  // Lingkar lengan atas anak dalam satuan sentimeter (cm)
            $table->enum('nutrition_status', ['Baik', 'Buruk', 'Kurang', 'Lebih']);  // Status gizi anak: Baik, Buruk, Kurang, atau Lebih
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->unsignedBigInteger('officer_id')->nullable(); // ID petugas yang melakukan penimbangan
            $table->timestamps(); // Waktu pembuatan dan pembaruan data

            // Menambahkan relasi ke tabel 'family_children' untuk kolom 'children_id', dan jika data anak dihapus, data penimbangan ini juga ikut dihapus
            $table->foreign('children_id')->references('id')->on('family_children')->onDelete('cascade');
            // Menambahkan relasi ke tabel 'officers' untuk kolom 'officer_id', dan jika petugas dihapus, kolom ini akan di-set null
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weighings');
    }
};
