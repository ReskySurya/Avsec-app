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
        // Tabel utama untuk logbook bulanan dengan kolom tanggal
        Schema::create('logbook_sweeping_pi', function (Blueprint $table) {
            $table->string('sweepingpiID', 20)->primary();
            $table->string('tenantID', 10);
            $table->integer('bulan'); // 1-12 untuk bulan
            $table->integer('tahun'); // tahun (misal: 2025)
            $table->timestamps();

            $table->foreign('tenantID')->references('tenantID')->on('tenants')->onDelete('cascade');

            // Index untuk optimasi query
            $table->index(['tenantID', 'bulan', 'tahun']);
            $table->unique(['tenantID', 'bulan', 'tahun']); // Pastikan hanya 1 record per tenant per bulan/tahun
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_sweeping_pi');
    }
};
