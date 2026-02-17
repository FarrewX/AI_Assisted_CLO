<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('generates', function (Blueprint $table) {
            $table->unsignedBigInteger('courseyear_id_ref');
            $table->longText('ai_text');
            $table->timestamps();

            $table->foreign('courseyear_id_ref')->references('courseyear_id_ref')->on('prompts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generates');
    }
};
