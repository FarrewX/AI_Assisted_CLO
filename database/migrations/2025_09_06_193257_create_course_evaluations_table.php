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
        Schema::create('course_evaluations', function (Blueprint $table) {
            $table->unsignedBigInteger('courseyear_id_ref');
            $table->text('feedback')->nullable()->comment('ข้อเสนอแนะ (s3)'); 
            $table->text('improvement')->nullable()->comment('การปรับปรุง (s3)');
            $table->text('agreement')->nullable()->comment('ข้อตกลงร่วม (s4)');
            $table->json('curriculum_map_data')->nullable()->comment('ข้อมูล Curriculum Map (s5_2)');
            $table->timestamps();

            $table->foreign('courseyear_id_ref')->references('id')->on('courseyears')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_evaluations');
    }
};
