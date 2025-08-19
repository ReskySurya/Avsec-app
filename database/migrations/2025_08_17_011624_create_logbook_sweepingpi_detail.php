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
        Schema::create('logbook_sweeping_pi_detail', function (Blueprint $table) {
            $table->id();
            $table->string('sweepingpiID', 20);
            $table->string('item_name_pi');
            $table->integer('quantity');
            for ($i = 1; $i <= 31; $i++) {
                $table->tinyInteger("tanggal_$i")->nullable();
            }
            $table->timestamps();

            $table->foreign('sweepingpiID')->references('sweepingpiID')->on('logbook_sweeping_pi')->onDelete('cascade');
            $table->index(['sweepingpiID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_sweeping_pi_detail');
    }
};
