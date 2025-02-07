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
            $table->date('ikf_tgl');
            $table->string('ikf_judul_konten', 150);
            $table->string('ikf_ringkasan', 150);
            $table->string('ikf_referensi')->nullable();
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
