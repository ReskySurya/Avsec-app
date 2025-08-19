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
        Schema::create('notes_sweeping_pi', function (Blueprint $table) {
            $table->id();
            $table->string('sweepingpiID', 20);
            $table->date('tanggal');
            $table->text('notes');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('sweepingpiID')
                ->references('sweepingpiID')
                ->on('logbook_sweeping_pi')
                ->onDelete('cascade');

            // Index for better performance
            $table->index(['sweepingpiID', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes_sweeping_pi');
    }
};
