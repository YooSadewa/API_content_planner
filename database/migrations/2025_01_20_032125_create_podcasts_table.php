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
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id('pdc_id');
            $table->date('pdc_jadwal_shoot');
            $table->date('pdc_jadwal_upload')->nullable();
            $table->string('pdc_tema', 150);
            $table->string('pdc_abstrak', 150)->nullable();
            $table->string('pdc_host');
            $table->string('pdc_speaker');
            $table->text('pdc_link')->nullable();
            $table->text('pdc_catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
