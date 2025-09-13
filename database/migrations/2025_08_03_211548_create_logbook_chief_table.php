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
            $table->string('logbookID', 20)->primary(); // Format: LTL-DDMMYY-0X
            $table->date('date');
            $table->string('grup')->nullable();
            $table->string('shift')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('senderSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['date', 'created_by']);
        });

        Schema::create('logbook_chief_kemajuan', function (Blueprint $table) {
            $table->id();
            $table->string('logbook_chief_id', 20);
            $table->integer('jml_personil');
            $table->integer('jml_hadir');
            $table->text('materi');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('logbook_chief_id')->references('logbookID')->on('logbook_chief')->onDelete('cascade');

            // Index
            $table->index('logbook_chief_id');
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
