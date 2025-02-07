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
        Schema::create('ide_konten_video', function (Blueprint $table) {
            $table->id('ikv_id');
            $table->date('ikv_tgl');
            $table->string('ikv_judul_konten', 150);
            $table->string('ikv_ringkasan', 150);
            $table->binary('ikv_foto');
            $table->enum('ikv_status', ['scheduled', 'on hold', 'done']);
            $table->string('ikv_skrip');
            $table->date('ikv_upload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ide_konten_video');
    }
};
