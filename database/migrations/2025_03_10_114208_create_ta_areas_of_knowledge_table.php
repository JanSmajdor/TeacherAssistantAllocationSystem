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
        Schema::create('ta_areas_of_knowledge', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ta_id')->nullable(false);
            $table->unsignedBigInteger('area_id')->nullable(false);

            $table->foreign('ta_id')->references('id')->on('teaching_assistants')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas_of_knowledge')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ta_areas_of_knowledge');
    }
};
