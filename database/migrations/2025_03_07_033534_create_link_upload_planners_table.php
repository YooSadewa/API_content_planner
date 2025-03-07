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
        Schema::create('link_upload_planners', function (Blueprint $table) {
            $table->id('lup_id');
            $table->unsignedBigInteger('onp_id');
            $table->text('lup_instagram')->nullable();
            $table->text('lup_facebook')->nullable();
            $table->text('lup_twitter')->nullable();
            $table->text('lup_youtube')->nullable();
            $table->text('lup_website')->nullable();
            $table->text('lup_tiktok')->nullable();
            $table->timestamps();

            $table->foreign('onp_id')->references('onp_id')->on('online_planners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_upload_planners');
    }
};
