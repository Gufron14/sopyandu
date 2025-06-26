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
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('children_id'); // Relasi ke tabel anak yang menerima imunisasi
            $table->date('immunization_date')->nullable(); // Tanggal imunisasi, opsional
            $table->string('age_in_checks')->nullable(); // Usia anak saat menerima imunisasi, misalnya dalam format "1 tahun 1 bulan 10 hari"
            $table->unsignedBigInteger('vaccine_id')->nullable(); // Relasi ke tabel vaksin yang digunakan
            $table->enum('vaccine_category', ['-', 'Wajib', 'Tambahan', 'Khusus', 'Lainnya']); // Kategori vaksin, misalnya Wajib, Tambahan, Khusus, atau Lainnya
            $table->text('side_effects')->nullable(); // Efek samping yang terjadi setelah imunisasi, jika ada
            $table->text('notes')->nullable(); // Catatan tambahan mengenai imunisasi jika diperlukan
            $table->unsignedBigInteger('officer_id')->nullable(); // ID petugas yang melakukan imunisasi
            $table->timestamps(); // Waktu pembuatan dan pembaruan data

            // Menambahkan relasi ke tabel 'family_children' untuk kolom 'children_id', dan jika data anak dihapus, data imunisasi ini juga ikut dihapus
            $table->foreign('children_id')->references('id')->on('family_children')->onDelete('cascade');
            // Menambahkan relasi ke tabel 'vaccines' untuk kolom 'vaccine_id', dan jika data vaksin dihapus, kolom ini akan di-set null
            $table->foreign('vaccine_id')->references('id')->on('vaccines')->onDelete('set null');
            // Menambahkan relasi ke tabel 'officers' untuk kolom 'officer_id', dan jika petugas dihapus, kolom ini akan di-set null
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunizations');
    }
};
