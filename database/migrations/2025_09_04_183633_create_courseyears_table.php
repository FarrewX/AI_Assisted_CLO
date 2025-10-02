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
            $table->id();
            $table->string('user_id',20)->default('unknown');
            $table->string('course_id',20);
            $table->string('term',10);
            $table->string('clo',10);
            $table->year('year');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            $table->unique(['user_id', 'course_id', 'year', 'term', 'clo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courseyears');
    }
};
