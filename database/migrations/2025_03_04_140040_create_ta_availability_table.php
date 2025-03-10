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
        Schema::create('ta_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ta_id');
            $table->datetime('available_from');
            $table->datetime('available_to');
            $table->timestamps();

            $table->foreign('ta_id')->references('id')->on('teaching_assistants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ta_availability');
    }
};
