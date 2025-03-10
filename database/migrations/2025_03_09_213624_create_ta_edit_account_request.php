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
        Schema::create('ta_edit_areas_of_knowledge_request', function (Blueprint $table) {
            $table->id();
            $table->integer('ta_id')->unsigned();
            $table->integer('area_id')->unsigned();
            $table->enum('request_status', ['Pending', 'Approved', 'Rejected']);
            $table->timestamps();

            $table->foreign('ta_id')->references('id')->on('teaching_assistants')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas_of_knowledge')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ta_edit_areas_of_knowledge_request');
    }
};
