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
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('tenantID', 10)->primary();
            $table->string('tenant_name')->nullable();
            $table->text('supervisorSignature')->nullable();
            $table->string('supervisorName')->nullable();
            $table->timestamps();

            $table->index('tenantID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('tenants');
    }
};
