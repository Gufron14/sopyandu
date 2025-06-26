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
        Schema::create('pregnancy_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->date('check_date');
            $table->string('age_in_checks')->nullable();
            $table->integer('gestational_age')->nullable();
            $table->float('mother_weight')->nullable();
            $table->float('mother_height')->nullable();
            $table->float('bmi')->nullable();
            $table->string('bmi_status')->nullable(); // Status IMT
            $table->string('blood_pressure')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->float('blood_sugar')->nullable();
            $table->float('cholesterol')->nullable();
            $table->string('fundus_height')->nullable();
            $table->string('fetal_heart_rate')->nullable();
            $table->enum('status_vaksin', ['Sudah', 'Sekarang', 'Tidak']);
            $table->enum('jenis_vaksin', ['Wajib', 'Tambahan', 'Khusus'])->nullable();
            $table->unsignedBigInteger('vaccine_id')->nullable(); // Relasi ke tabel vaccines
            $table->enum('fetal_presentation', ['Kepala', 'Bokong', 'Lainnya']);
            $table->enum('edema', ['Tidak', 'Ringan', 'Sedang', 'Berat']);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('family_parents')->onDelete('cascade');
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('set null');
            $table->foreign('vaccine_id')->references('id')->on('vaccines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnancy_checks');
    }
};
