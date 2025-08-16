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
        // Tabel utama logbook
        Schema::create('logbook_rotasi_hbscp', function (Blueprint $table) {
            $table->string('id')->primary(); // Format: LRH-00001
            $table->date('date');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('date');
            $table->index('status');
        });

        // Tabel detail logbook (baris-baris dalam form)
        Schema::create('logbook_rotasi_hbscp_details', function (Blueprint $table) {
            $table->id();
            $table->string('logbook_id');
            $table->time('start')->nullable();
            $table->time('end')->nullable();

            // Officer assignments
            $table->unsignedBigInteger('pengatur_flow')->nullable();
            $table->unsignedBigInteger('operator_xray')->nullable();
            $table->unsignedBigInteger('manual_bagasi_petugas')->nullable();
            $table->unsignedBigInteger('reunited')->nullable();

            // Notes
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('logbook_id')->references('id')->on('logbook_rotasi_hbscp')->onDelete('cascade');
            $table->foreign('pengatur_flow')->references('id')->on('users')->onDelete('set null');
            $table->foreign('operator_xray')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manual_bagasi_petugas')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reunited')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('logbook_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_rotasi_hbscp_details');
        Schema::dropIfExists('logbook_rotasi_hbscp');
    }
};
