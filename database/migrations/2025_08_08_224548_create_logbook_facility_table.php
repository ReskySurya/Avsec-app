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
        Schema::create('logbook_facility', function (Blueprint $table) {
            $table->id();
            $table->string('logbookID', 10)->nullable();
            $table->string('logbook_chief_id', 20)->nullable();
            $table->string('facility')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('logbookID')->references('logbookID')->on('logbooks')->onDelete('cascade');
            $table->foreign('logbook_chief_id')->references('logbookID')->on('logbook_chief')->onDelete('cascade');

            $table->index('logbookID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_facility');
    }
};
