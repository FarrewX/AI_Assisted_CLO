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
        Schema::create('feedback', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id');
            $table->text('feedback')->nullable()->comment('ข้อเสนอแนะ (s3)');
            $table->text('improvement')->nullable()->comment('การปรับปรุง (s3)');
            $table->text('agreement')->nullable()->comment('ข้อตกลงร่วม');
            $table->text('research')->nullable()->comment('งานวิจัย (s9_1)');
            $table->text('research_subjects')->nullable()->comment('งานวิจัยที่นำมาสอน (s9_2)');
            $table->text('academic_service')->nullable()->comment('บริการวิชาการ (s9_3)');
            $table->text('art_culture')->nullable()->comment('ทำนุบำรุงศิลปวัฒนธรรม (s9_4)');
            $table->timestamps();

            $table->foreign('ref_id')->references('ref_id')->on('curricula')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
