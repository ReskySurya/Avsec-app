<?php

// File: database/migrations/xxxx_xx_xx_create_logbook_rotasi_pscp_tables.php

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
        Schema::create('logbook_rotasi_pscps', function (Blueprint $table) {
            $table->string('id')->primary(); // Format: LRP-00001
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
        Schema::create('logbook_rotasi_pscp_details', function (Blueprint $table) {
            $table->id();
            $table->string('logbook_id');
            $table->time('start')->nullable();
            $table->time('end')->nullable();

            // Officer assignments
            $table->unsignedBigInteger('pemeriksaan_dokumen')->nullable();
            $table->unsignedBigInteger('pengatur_flow')->nullable();
            $table->unsignedBigInteger('operator_xray')->nullable();
            $table->unsignedBigInteger('hhmd_petugas')->nullable();
            $table->unsignedBigInteger('manual_kabin_petugas')->nullable();

            // Counters
            $table->integer('hhmd_random')->nullable();
            $table->integer('hhmd_unpredictable')->nullable();
            $table->integer('cek_random_barang')->nullable();
            $table->integer('barang_unpredictable')->nullable();

            // Notes
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('logbook_id')->references('id')->on('logbook_rotasi_pscps')->onDelete('cascade');
            $table->foreign('pemeriksaan_dokumen')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pengatur_flow')->references('id')->on('users')->onDelete('set null');
            $table->foreign('operator_xray')->references('id')->on('users')->onDelete('set null');
            $table->foreign('hhmd_petugas')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manual_kabin_petugas')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('logbook_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_rotasi_pscp_details');
        Schema::dropIfExists('logbook_rotasi_pscps');
    }
};
