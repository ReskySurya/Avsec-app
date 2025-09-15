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
        Schema::create('form_pencatatan_pi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_pencatatan_pi_id')->constrained('form_pencatatan_pi')->onDelete('cascade');
            $table->string('jenis_pi');
            $table->string('in_quantity');
            $table->string('out_quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_pencatatan_pi_details');
    }
};