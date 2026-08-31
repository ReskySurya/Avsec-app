<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // logbooks - index untuk filter status, date range, dan ORDER BY
        Schema::table('logbooks', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
            $table->index('approvedID');
            $table->index('receivedID');
            $table->index('shift');
            $table->index(['status', 'date']);
        });

        // logbook_chief - index untuk filter status, date range, dan supervisor queries
        Schema::table('logbook_chief', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
            $table->index('approved_by');
        });

        // logbook_facility - FK logbook_chief_id tidak ada index
        Schema::table('logbook_facility', function (Blueprint $table) {
            $table->index('logbook_chief_id');
        });

        // logbook_rotasi - index untuk filter by created_by, approved_by, date
        // Note: status index sudah ada dari migration awal
        Schema::table('logbook_rotasi', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('approved_by');
            $table->index('created_at');
        });

        // logbook_staff - index untuk filter description 'hadir' dan duplicate check
        Schema::table('logbook_staff', function (Blueprint $table) {
            $table->index('description');
            $table->index(['logbook_chief_id', 'staffID']);
        });

        // logbook_sweeping_pi - index untuk ORDER BY created_at
        Schema::table('logbook_sweeping_pi', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['approvedID']);
            $table->dropIndex(['receivedID']);
            $table->dropIndex(['shift']);
            $table->dropIndex(['status', 'date']);
        });

        Schema::table('logbook_chief', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['approved_by']);
        });

        Schema::table('logbook_facility', function (Blueprint $table) {
            $table->dropIndex(['logbook_chief_id']);
        });

        Schema::table('logbook_rotasi', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['approved_by']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('logbook_staff', function (Blueprint $table) {
            $table->dropIndex(['description']);
            $table->dropIndex(['logbook_chief_id', 'staffID']);
        });

        Schema::table('logbook_sweeping_pi', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
