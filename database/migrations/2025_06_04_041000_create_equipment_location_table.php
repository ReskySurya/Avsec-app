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
        Schema::create('equipment_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipment_id');
            $table->unsignedBigInteger('location_id');
            $table->string('merk_type', 255)->nullable();
            $table->string('certificateInfo', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            
            // Ensure unique combination
            $table->unique(['equipment_id', 'location_id'], 'equipment_location_unique');
            
            // Add indexes for better performance
            $table->index('equipment_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_locations');
    }
};