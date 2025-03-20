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
        Schema::create('analytic_content', function (Blueprint $table) {
            $table->id('anc_id');
            $table->unsignedBigInteger('lup_id');
            $table->date('anc_tanggal');
            $table->string('anc_hari');
            $table->timestamps();

            $table->foreign('lup_id')->references('lup_id')->on('link_upload_planners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_content');
    }
};
