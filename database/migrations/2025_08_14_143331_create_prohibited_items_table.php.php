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
        Schema::create('prohibited_items', function (Blueprint $table) {
            $table->id();
            $table->string('tenantID', 10);
            $table->string('items_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->foreign('tenantID')->references('tenantID')->on('tenants')->onDelete('cascade');

            $table->index('tenantID');
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::dropIfExists('prohibited_items');
    }
};
