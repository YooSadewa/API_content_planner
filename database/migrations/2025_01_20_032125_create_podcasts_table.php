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
            $table->unsignedBigInteger('pmb_id');
            $table->foreign('pmb_id')->references('pmb_id')->on('pembicaras')->onDelete('cascade');
            $table->unsignedBigInteger('host_id');
            $table->foreign('host_id')->references('host_id')->on('hosts')->onDelete('cascade');
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
