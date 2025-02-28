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
        Schema::create('ide_konten_foto', function (Blueprint $table) {
            $table->id('ikf_id');
            $table->date('ikf_tgl')->nullable();
            $table->string('ikf_judul_konten', 150);
            $table->string('ikf_ringkasan', 150);
            $table->string('ikf_pic');
            $table->enum('ikf_status', ['scheduled', 'on hold', 'done'])->default('scheduled');
            $table->string('ikf_skrip')->nullable();
            $table->string('ikf_referensi')->nullable();
            $table->date('ikf_upload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ide_konten_foto');
    }
};
