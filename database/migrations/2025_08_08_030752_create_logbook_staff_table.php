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
        Schema::create('logbook_staff', function (Blueprint $table) {
            $table->id();
            $table->string('logbookID', 10);
            $table->unsignedBigInteger('staffID')->nullable();
            $table->string('classification')->nullable(); 
            $table->enum('description', ['hadir', 'izin', 'sakit','cuti'])->default('hadir'); 
            $table->timestamps();

            $table->foreign('logbookID')->references('logbookID')->on('logbooks')->onDelete('cascade');
            $table->foreign('staffID')->references('id')->on('users')->onDelete('cascade');
            
            // Index
            $table->index('logbookID');
            $table->index('staffID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_staff');
    }
};
