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
        Schema::create('family_parents', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('mother_fullname')->nullable();
            $table->string('mother_birth_place')->nullable();
            $table->date('mother_date_of_birth')->nullable();
            $table->enum('mother_blood_type', ['-', 'A', 'B', 'AB', 'O']);
            $table->string('father_nik')->nullable();
            $table->string('father_fullname')->nullable();
            $table->string('father_birth_place')->nullable();
            $table->date('father_date_of_birth')->nullable();
            $table->enum('father_blood_type', ['-', 'A', 'B', 'AB', 'O']);
            $table->enum('is_pregnant', ['Tidak Hamil', 'Hamil']);
            $table->integer('number_of_children')->nullable();
            $table->string('address')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('village')->nullable();
            $table->string('hamlet')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_parents');
    }
};
