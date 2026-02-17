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
        Schema::create('course_types', function (Blueprint $table) {
            $table->unsignedBigInteger('CC_id_ref');
            $table->boolean('specific')->comment('วิชาเฉพาะ')->nullable();
            $table->boolean('core')->comment('กลุ่มวิชาแกน')->nullable();
            $table->boolean('major_required')->comment('เอกบังคับ')->nullable();
            $table->boolean('major_elective')->comment('เอกเลือก')->nullable();
            $table->boolean('free_elective')->comment('วิชาเลือกเสรี')->nullable();
            $table->text('prerequisites')->comment('วิชาบังคับก่อน')->nullable();
            $table->integer('hours_theory')->comment('ชั่วโมงภาคทฤษฎี')->nullable();
            $table->integer('hours_practice')->comment('ชั่วโมงภาคปฏิบัติ')->nullable();
            $table->integer('hours_self_study')->comment('ชั่วโมงการศึกษาด้วยตนเอง')->nullable();
            $table->integer('hours_field_trip')->comment('ชั่วโมงทัศนศึกษา/ฝึกงาน')->nullable();

            $table->timestamps();

            $table->foreign('CC_id_ref')->references('id')->on('curriculum_courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_types');
    }
};
