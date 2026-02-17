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
        Schema::create('course_learning_maps', function (Blueprint $table) {
            $table->unsignedBigInteger('courseyear_id_ref');
            $table->json('course_accord')->nullable()->comment('CLO + LLL mapping data')->nullable();
            $table->timestamps();

            $table->foreign('courseyear_id_ref')->references('id')->on('courseyears')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_learning_maps');
    }
};
