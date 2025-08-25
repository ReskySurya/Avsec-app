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
        Schema::create('checklist_senpi', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->date('date');
            $table->string('name')->nullable();
            $table->string('agency')->nullable();
            $table->string('flightNumber')->nullable();
            $table->string('destination')->nullable();
            $table->string('typeSenpi')->nullable();
            $table->integer('quantitySenpi')->nullable();
            $table->integer('quantityMagazine')->nullable();
            $table->integer('quantityBullet')->nullable();
            $table->string('licenseNumber')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_senpi');
    }
};
