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
            $table->unsignedBigInteger('CC_id');
            $table->string('user_id');
            $table->string('year')->nullable();
            $table->string('term')->nullable();
            $table->string('TQF')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('CC_id')->references('id')->on('curriculum_courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courseyears');
    }
};
