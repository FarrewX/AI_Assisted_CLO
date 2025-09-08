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
       Schema::create('statuses', function (Blueprint $table) {
            $table->string('course_id');
            $table->timestamp('startprompt')->nullable();
            $table->timestamp('generated')->nullable();
            $table->timestamp('downloaded')->nullable();
            $table->timestamp('success')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
