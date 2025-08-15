<?php

// database/migrations/2025_08_14_000001_create_tb_detail_logbook_rotasipscp_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_logbook_rotasipscp', function (Blueprint $table) {
            $table->id();
            $table->string('logbook_id', 15); // Relasi ke tb_logbook_rotasipscp.id
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->unsignedBigInteger('pemeriksaan_dokumen')->nullable();
            $table->unsignedBigInteger('pengatur_flow')->nullable();
            $table->unsignedBigInteger('operator_xray')->nullable();
            $table->unsignedBigInteger('hhmd_petugas')->nullable();
            $table->integer('hhmd_random')->nullable();
            $table->integer('hhmd_unpredictable')->nullable();
            $table->unsignedBigInteger('manual_kabin_petugas')->nullable();
            $table->integer('cek_random_barang')->nullable();
            $table->integer('barang_unpredictable')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Relasi ke tabel header
            $table->foreign('logbook_id')->references('id')->on('logbook_rotasipscp')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_logbook_rotasipscp');
    }
};

