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
        Schema::create('analytic_content_report', function (Blueprint $table) {
            $table->id('acr_id');
            $table->unsignedBigInteger('anc_id');
            $table->set('acr_platform', ['website', 'instagram', 'twitter', 'facebook', 'youtube', 'tiktok']);
            $table->string('acr_reach');
            $table->string('acr_like')->nullable();
            $table->string('acr_comment')->nullable();
            $table->string('acr_share')->nullable();
            $table->string('acr_save')->nullable();
            $table->timestamps();

            $table->foreign('anc_id')->references('anc_id')->on('analytic_content')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_content_report');
    }
};
