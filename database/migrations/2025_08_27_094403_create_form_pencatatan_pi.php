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
        Schema::create('form_pencatatan_pi', function (Blueprint $table) {
            $table->id()->primary();
            $table->date('date');
            $table->string('grup')->nullable();
            $table->string('in_time')->nullable(); 
            $table->string('out_time')->nullable();
            $table->string('name_person')->nullable();
            $table->string('agency')->nullable();
            $table->string('jenis_PI')->nullable();
            $table->string('in_quantity')->nullable();
            $table->string('out_quantity')->nullable();
            $table->string('summary')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('approved_id')->nullable();
            $table->text('senderSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_pencatatan_pi');
    }
};
