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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('module_leader_id');
            $table->unsignedBigInteger('request_batch_id');
            $table->integer('num_tas_requested');
            $table->datetime('date_from');
            $table->datetime('date_to');
            $table->enum('booking_type', ['Lecture', 'Lab', 'Seminar', 'Marking', 'Other']);
            $table->enum('site', ['Site 1', 'Site 2', 'Site 3']);
            $table->enum('status', ['Pending', 'Approved', 'Denied'])->default('Pending');
            $table->timestamps();
        
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('module_leader_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
