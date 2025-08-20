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
        Schema::create('checklist_kendaraan_details', function (Blueprint $table) {
            $table->id();
            $table->string('checklist_kendaraan_id');
            $table->unsignedBigInteger('checklist_item_id');
            $table->boolean('is_ok')->comment('true: Baik, false: Rusak');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('checklist_kendaraan_id')->references('id')->on('checklist_kendaraan')->onDelete('cascade');
            $table->foreign('checklist_item_id')->references('id')->on('checklist_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_kendaraan_details');
    }
};
