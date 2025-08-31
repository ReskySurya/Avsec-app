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
        // Tabel untuk officer assignments (jadwal shift)
        Schema::create('officer_rotasi_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('officer_id');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('shift_type', ['pagi', 'siang', 'malam'])->nullable();
            $table->enum('location_type', ['hbscp', 'pscp']);
            $table->timestamps();

            // Foreign keys
            $table->foreign('officer_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['officer_id', 'date']);
            $table->index(['date', 'location_type']);
            $table->index('location_type');
        });

        // Tabel utama logbook (gabungan HBSCP dan PSCP)
        Schema::create('logbook_rotasi', function (Blueprint $table) {
            $table->string('id')->primary(); // Format: LRH-00001 (HBSCP) atau LRP-00001 (PSCP)
            $table->enum('type', ['hbscp', 'pscp']); // Tipe logbook
            $table->date('date');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('submittedSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['date', 'type']);
            $table->index('status');
            $table->index('type');
        });

        // Tabel detail logbook (gabungan baris-baris untuk HBSCP dan PSCP)
        Schema::create('logbook_rotasi_details', function (Blueprint $table) {
            $table->id();
            $table->string('logbook_id');
            $table->unsignedBigInteger('officer_assignment_id'); // Referensi ke officer_rotasi_assignments

            // HBSCP Officer assignments
            $table->unsignedBigInteger('pengatur_flow')->nullable();
            $table->unsignedBigInteger('operator_xray')->nullable();
            $table->unsignedBigInteger('manual_bagasi_petugas')->nullable();
            $table->unsignedBigInteger('reunited')->nullable();

            // PSCP Officer assignments
            $table->unsignedBigInteger('pemeriksaan_dokumen')->nullable();
            $table->unsignedBigInteger('hhmd_petugas')->nullable();
            $table->unsignedBigInteger('manual_kabin_petugas')->nullable();

            // PSCP Counters (hanya untuk PSCP)
            $table->integer('hhmd_random')->nullable();
            $table->integer('hhmd_unpredictable')->nullable();
            $table->integer('cek_random_barang')->nullable();
            $table->integer('barang_unpredictable')->nullable();

            // Notes
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('logbook_id')->references('id')->on('logbook_rotasi')->onDelete('cascade');
            $table->foreign('officer_assignment_id')->references('id')->on('officer_rotasi_assignments')->onDelete('cascade');

            // HBSCP Foreign keys
            $table->foreign('pengatur_flow')->references('id')->on('users')->onDelete('set null');
            $table->foreign('operator_xray')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manual_bagasi_petugas')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reunited')->references('id')->on('users')->onDelete('set null');

            // PSCP Foreign keys
            $table->foreign('pemeriksaan_dokumen')->references('id')->on('users')->onDelete('set null');
            $table->foreign('hhmd_petugas')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manual_kabin_petugas')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('logbook_id');
            $table->index('officer_assignment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_rotasi_details');
        Schema::dropIfExists('logbook_rotasi');
        Schema::dropIfExists('officer_rotasi_assignments');
    }
};
