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
        Schema::create('family_children', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('fullname')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->enum('blood_type', ['-', 'A', 'B', 'AB', 'O']);
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('family_parents')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_children');
    }
};
