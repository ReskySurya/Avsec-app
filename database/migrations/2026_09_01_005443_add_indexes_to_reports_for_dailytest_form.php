<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index('approvedByID');
            $table->index(['statusID', 'approvedByID']);
            $table->index(['approvedByID', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['approvedByID']);
            $table->dropIndex(['statusID', 'approvedByID']);
            $table->dropIndex(['approvedByID', 'created_at']);
        });
    }
};
