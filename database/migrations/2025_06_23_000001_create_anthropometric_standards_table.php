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
        Schema::create('anthropometric_standards', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['BB/U', 'TB/U', 'BB/TB', 'IMT/U']);
            $table->enum('gender', ['L', 'P']);
            $table->float('value'); // Age in months or height in cm
            $table->float('minus_3sd');
            $table->float('minus_2sd');
            $table->float('median');
            $table->float('plus_2sd');
            $table->float('plus_3sd');
            $table->timestamps();

            // Composite index for faster lookups
            $table->index(['type', 'gender', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anthropometric_standards');
    }
};
