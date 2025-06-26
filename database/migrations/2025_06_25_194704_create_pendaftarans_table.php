<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pendaftaran');
            $table->enum('sasaran', ['balita', 'ibu_hamil']);
            $table->unsignedBigInteger('children_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('status', ['menunggu', 'sudah_dilayani'])->default('menunggu');
            $table->boolean('status_changed')->default(false);
            $table->timestamps();

            $table->foreign('children_id')->references('id')->on('family_children')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('family_parents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pendaftarans');
    }
};

