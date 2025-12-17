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
        // Schema::create('curricula', function (Blueprint $table) {
        //     // ข้อมูลหมวดที่ 1 (S1)
        //     $table->unsignedBigInteger('ref_id');
        //     $table->string('curriculum_name')->nullable(); //วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์
        //     $table->string('faculty')->nullable(); //วิทยาศาสตร์
        //     $table->string('major')->nullable(); //สาขาวิชาวิทยาการคอมพิวเตอร์
        //     $table->string('campus')->nullable(); //เชียงใหม่
        //     $table->string('credits')->nullable(); //หน่วยกิต
        //     $table->char('curriculum_year', 4)->comment('หลักสูตรปรับปรุง')->nullable();
        //     $table->boolean('is_specific')->default(0)->comment('วิชาเฉพาะ')->nullable();
        //     $table->boolean('is_core')->default(0)->comment('กลุ่มวิชาแกน')->nullable();
        //     $table->boolean('is_major_required')->default(0)->comment('เอกบังคับ')->nullable();
        //     $table->boolean('is_major_elective')->default(0)->comment('เอกเลือก')->nullable();
        //     $table->boolean('is_free_elective')->default(0)->comment('วิชาเลือกเสรี')->nullable();
        //     $table->text('prerequisites')->comment('วิชาบังคับก่อน (s1_prerequisites)')->nullable();
        //     $table->integer('hours_theory')->default(0)->comment('ชั่วโมงภาคทฤษฎี')->nullable();
        //     $table->integer('hours_practice')->default(0)->comment('ชั่วโมงภาคปฏิบัติ')->nullable();
        //     $table->integer('hours_self_study')->default(0)->comment('ชั่วโมงการศึกษาด้วยตนเอง')->nullable();
        //     $table->integer('hours_field_trip')->default(0)->comment('ชั่วโมงทัศนศึกษา/ฝึกงาน')->nullable();

        //     // ข้อมูลหมวดที่ 5 (S5)
        //     $table->json('outcome_statement')->nullable()->comment('ผลลัพธ์การเรียนรู้ของหลักสูตร (s5_1)');
        //     $table->json('curriculum_map_data')->nullable()->comment('ข้อมูล Curriculum Map (s5_2)');
        //     $table->json('course_accord')->nullable()->comment('ความสอดคล้องของรายวิชากับ PLO (s5_3)');
            
        //     $table->timestamps();

        //     $table->foreign('ref_id')->references('id')->on('courseyears')->onDelete('cascade');

        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curricula');
    }
};
