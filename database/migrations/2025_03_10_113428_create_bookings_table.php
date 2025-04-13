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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('module_leader_id');
            $table->integer('num_tas_requested')->nullable(false);
            $table->datetime('date_from')->nullable(false);
            $table->datetime('date_to')->nullable(false);
            $table->enum('booking_type', ['Lecture', 'Lab', 'Seminar', 'Marking', 'Other'])->nullable(false);
            $table->enum('site', ['Site 1', 'Site 2', 'Site 3'])->nullable(false);
            $table->unsignedBigInteger('request_batch_id');
            $table->enum('status', ['Auto Matched', 'Pending Manual Assignment', 'Approved', 'Denied', 'Pending'])->default('Pending')->nullable(false);
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
        Schema::dropIfExists('bookings');
    }
};
