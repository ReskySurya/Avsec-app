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
        Schema::create('checklist_penyisiran', function (Blueprint $table) {
            $table->string('id')->primary(); // Format: PSR-YYMMDD-A-XXXX
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('type')->nullable(); // untuk keperluan masa depan
            $table->string('grup')->nullable(); // A, B, C
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('received_id')->nullable();
            $table->unsignedBigInteger('approved_id')->nullable();
            $table->text('senderSignature')->nullable();
            $table->text('receivedSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('received_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_penyisiran');
    }
};
