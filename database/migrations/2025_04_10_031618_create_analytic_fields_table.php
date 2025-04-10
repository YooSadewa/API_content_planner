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
        Schema::create('analytic_fields', function (Blueprint $table) {
            $table->id('anf_id');
            $table->unsignedBigInteger('anp_id');
            $table->foreign('anp_id')->references('anp_id')->on('analytic_platforms')->onDelete('cascade');
            $table->string('anf_name');
            $table->boolean('anf_required')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_fields');
    }
};
