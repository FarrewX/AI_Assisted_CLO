<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('generates', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id');
            $table->longText('ai_text');
            $table->timestamps();

            $table->foreign('ref_id')->references('ref_id')->on('prompts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generates');
    }
};
