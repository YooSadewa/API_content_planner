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
        Schema::create('detail_platform', function (Blueprint $table) {
            $table->id('dpl_id');
            $table->unsignedBigInteger('dacc_id'); // Foreign key ke detail_account
            $table->enum('dpl_platform', ['website', 'instagram', 'twitter', 'facebook', 'youtube', 'tiktok']);
            $table->bigInteger('dpl_total_konten');
            $table->bigInteger('dpl_pengikut')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('dacc_id')->references('dacc_id')->on('detail_account')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_platform');
    }
};
