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
        Schema::create('logbook_details', function (Blueprint $table) {
            $table->id();
            $table->string('logbookID', 10)->nullable();
            $table->string('logbook_chief_id', 20)->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('summary');
            $table->text('description');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('logbookID')->references('logbookID')->on('logbooks')->onDelete('cascade');
            $table->foreign('logbook_chief_id')->references('logbookID')->on('logbook_chief')->onDelete('cascade');

            // Index
            $table->index('logbookID');
            $table->index('logbook_chief_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_details');
    }
};
