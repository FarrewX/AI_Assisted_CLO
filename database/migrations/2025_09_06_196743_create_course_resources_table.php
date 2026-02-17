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
        Schema::create('course_resources', function (Blueprint $table) {
            $table->unsignedBigInteger('courseyear_id_ref');
            $table->text('research')->nullable()->comment('งานวิจัย (s9_1)');
            $table->text('research_subjects')->nullable()->comment('งานวิจัยที่นำมาสอน (s9_2)');
            $table->text('academic_service')->nullable()->comment('บริการวิชาการ (s9_3)');
            $table->text('art_culture')->nullable()->comment('ทำนุบำรุงศิลปวัฒนธรรม (s9_4)');
            $table->timestamps();

            $table->foreign('courseyear_id_ref')->references('id')->on('courseyears')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_resources');
    }
};
