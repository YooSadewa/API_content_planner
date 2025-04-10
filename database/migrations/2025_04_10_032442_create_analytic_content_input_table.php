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
        Schema::create('analytic_content_input', function (Blueprint $table) {
            $table->id('anc_id');
            $table->date('anc_tgl');
            $table->string('anc_hari');
            $table->unsignedBigInteger('lup_id');
            $table->foreign('lup_id')->references('lup_id')->on('link_upload_planners')->onDelete('cascade');
            $table->unsignedBigInteger('anf_id');
            $table->foreign('anf_id')->references('anf_id')->on('analytic_fields')->onDelete('cascade');
            $table->integer('value')->nullable();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_content_input');
    }
};
