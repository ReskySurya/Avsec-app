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
        Schema::create('checklist_penyisiran_detail', function (Blueprint $table) {
            $table->id();
            $table->string('checklist_penyisiran_id');
            $table->unsignedBigInteger('checklist_item_id');
            $table->boolean('isfindings')->nullable()->comment('true: Ya, false: Tidak (untuk temuan)');
            $table->boolean('iscondition')->nullable()->comment('true: Baik, false: Rusak (untuk kondisi)');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('checklist_penyisiran_id', 'fk_cpd_checklist_id')
                  ->references('id')
                  ->on('checklist_penyisiran')
                  ->onDelete('cascade');
                  
            $table->foreign('checklist_item_id', 'fk_cpd_item_id')
                  ->references('id')
                  ->on('checklist_items')
                  ->onDelete('cascade');

            // Index with custom name to avoid MySQL length limit
            $table->index(['checklist_penyisiran_id', 'checklist_item_id'], 'idx_cpd_checklist_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_penyisiran_detail');
    }
};