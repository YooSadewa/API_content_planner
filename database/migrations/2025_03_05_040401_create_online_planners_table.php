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
        Schema::create('online_planners', function (Blueprint $table) {
            $table->id('onp_id');
            $table->date('onp_tanggal');
            $table->string('onp_hari');
            $table->string('onp_topik_konten');
            $table->unsignedBigInteger('user_id');
            $table->set('onp_platform', ['website', 'instagram', 'twitter', 'facebook', 'youtube', 'tiktok']);
            $table->set('onp_checkpoint', ['jayaridho', 'gilang', 'chris', 'winny']);
            $table->unsignedBigInteger('lup_id');
            $table->timestamps();

            $table->foreign('lup_id')->references('lup_id')->on('link_upload_planners')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_planners');
    }
};
