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
        Schema::create('logbook_chief', function (Blueprint $table) {
            $table->string('id')->primary(); // Format: LTL-00001
            $table->string('grup')->nullable();
            $table->string('shift')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('senderSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');


        });

        Schema::create('logbook_chief_details', function (Blueprint $table) {
            $table->id();
            $table->string('logbook_id', 10);
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_chief_details');
        Schema::dropIfExists('logbook_chief');
    }
};
