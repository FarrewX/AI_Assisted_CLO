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
        Schema::create('courseyears', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id')->default('unknown');
            $table->string('course_id');
            $table->year('year');
            $table->timestamps();

            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            $table->unique(['course_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courseyears');
    }
};
