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
        Schema::create('prompts', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id');
            $table->string('course_id');
            $table->string('course_text');
            $table->timestamps();

            $table->foreign('ref_id')->references('id')->on('courseyears')->onDelete('cascade');
        });

        Schema::table('prompts', function (Blueprint $table) {
            $table->text('course_text')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
