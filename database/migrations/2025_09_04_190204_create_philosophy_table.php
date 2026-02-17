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
        Schema::create('philosophy', function (Blueprint $table) {
            $table->id();
            $table->string('curriculum_year_ref');
            $table->text('mju_philosophy')->comment('ปรัชญาแม่โจ้')->nullable();
            $table->text('education_philosophy')->comment('ปรัชญาการศึกษา')->nullable();
            $table->text('curriculum_philosophy')->comment('ปรัชญาหลักสูตร')->nullable();
            $table->timestamps();

            $table->foreign('curriculum_year_ref')->references('curriculum_year')->on('curriculum')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('philosophy');
    }
};
