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
        Schema::create('checklist_kendaraan', function (Blueprint $table) {
            $table->string('id')->primary(); // Format: CML-00001 (Mobil) atau CMT-00001 (Motor)
            $table->date('date');
            $table->enum('type', ['motor', 'mobil']); // Tipe Checklist Kendaraan
            $table->enum('shift', ['pagi', 'malam']);
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('sender_id')->nullable();;
            $table->unsignedBigInteger('received_id')->nullable();
            $table->unsignedBigInteger('approved_id')->nullable();
            $table->text('senderSignature')->nullable();
            $table->text('receivedSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('received_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['date', 'type', 'shift', 'status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_kendaraan');
    }
};
