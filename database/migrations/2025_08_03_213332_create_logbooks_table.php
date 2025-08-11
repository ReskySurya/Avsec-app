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
        Schema::create('logbooks', function (Blueprint $table) {
            $table->string('logbookID', 10)->primary(); // L-xxxxxxx format
            $table->date('date');
            $table->unsignedBigInteger('location_area_id');
            $table->string('grup');
            $table->string('shift');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft'); // Status of the logbook
            $table->unsignedBigInteger('senderID');
            $table->unsignedBigInteger('receivedID')->nullable();
            $table->unsignedBigInteger('approvedID')->nullable();
            $table->text('senderSignature')->nullable();
            $table->text('receivedSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->text('rejected_reason')->nullable(); // Reason for rejection, if applicable
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('location_area_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('senderID')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receivedID')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approvedID')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['date', 'location_area_id']);
            $table->index('senderID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
