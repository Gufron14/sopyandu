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
        Schema::create('officers', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique()->nullable();
            $table->string('fullname')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->enum('position', [
                'Admin',                    // Admin yang mengelola website
                'Lurah',                    // Kepala desa/kelurahan
                'Kepala Lingkungan',        // Kepala dusun/lingkungan
                'Bidan',                    // Petugas medis yang khusus menangani ibu dan anak
                'Tenaga Medis Puskesmas/Desa',   // Petugas medis dari puskesmas seperti dokter, ahli gizi, dan lain-lain
                'Kader'                     // Relawan yang membantu posyandu
            ]);
            $table->text('address')->nullable();
            $table->string('last_education')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officers');
    }
};
