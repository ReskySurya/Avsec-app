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
        Schema::create('reportStatus', function (Blueprint $table) {
            $table->id('id')->comment('Primary key for status');
            
            // Basic status information
            $table->string('name', 255)->unique()->comment('Unique status name (lowercase_with_underscores)');
            $table->string('label', 255)->comment('Display label for the status');
            $table->text('description')->nullable()->comment('Detailed description of the status');
            
            
            // Behavior settings
            $table->boolean('isDefault')->default(false)->comment('Whether this is the default status');

            
            // Timestamps and soft deletes
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('name');
            $table->index('isDefault');
            
            // Unique constraint to ensure only one default status
            $table->unique(['isDefault'], 'unique_default_status');
        });
        
        // Insert default statuses
        // $this->insertDefaultStatuses();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_status');
    }
};