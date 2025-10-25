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
        Schema::create('plans', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id');
            $table->json('teaching_methods')->nullable()->comment('วิธีการสอน (s6)');
            $table->json('lesson_plan')->nullable()->comment('แผนการสอน (s7)');
            $table->json('assessment_strategies')->nullable()->comment('กลยุทธ์การประเมิน (s8_1)');
            $table->json('rubrics')->nullable()->comment('วิธีการประเมิน แบบรูบริค (s8_2)');
            $table->json('grading_criteria')->nullable()->comment('เกณฑ์การประเมิน (s10)');
            $table->text('grade_correction')->nullable()->comment('ขั้นตอนการแก้ไขคะแนน (s11)');
            $table->timestamps();

            $table->foreign('ref_id')->references('ref_id')->on('curricula')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
