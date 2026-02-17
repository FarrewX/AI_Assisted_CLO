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
        Schema::create('curriculum_llls', function (Blueprint $table) {
            $table->unsignedBigInteger('curriculum_id_ref');
            $table->integer('num_LLL')->nullable();
            $table->string('name_LLL')->nullable();
            $table->timestamps();

            $table->foreign('curriculum_id_ref')->references('id')->on('curriculum')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_llls');
    }
};
