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
        Schema::create('plos', function (Blueprint $table) {
            $table->id();
            $table->string('curriculum_year_ref');
            $table->tinyInteger('plo');
            $table->string('description');
            $table->string('domain')->nullable();
            $table->string('learning_level')->nullable();
            $table->boolean('specific_lo')->default(false); // 1 = Specific LO, 0 = Not Specific LO
            $table->timestamps();

            $table->foreign('curriculum_year_ref')->references('curriculum_year')->on('curriculum')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plos');
    }
};
