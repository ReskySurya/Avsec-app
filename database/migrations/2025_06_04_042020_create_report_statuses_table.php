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
        Schema::create('report_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('label', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('isDefault')->default(false);
            $table->string('color', 20)->nullable(); // For UI color coding
            $table->timestamps();
            
            // Indexes
            $table->index(['name']);
            $table->index(['isDefault']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_statuses');
    }
};