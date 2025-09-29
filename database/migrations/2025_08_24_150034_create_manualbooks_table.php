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
        Schema::create('manualbooks', function (Blueprint $table) {
            $table->string('id')->primary(); // Format: MBH-00001 (HBSCP) atau MBP-00001 (PSCP)
            $table->enum('type', ['hbscp', 'pscp']); // Tipe logbook
            $table->enum('shift', ['pagi', 'malam']);
            $table->date('date');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('senderSignature')->nullable();
            $table->text('approvedSignature')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['date', 'type', 'shift']);
            $table->index('status');
            $table->index('type');
        });

        Schema::create('manualbook_details', function (Blueprint $table) {
            $table->id();
            $table->string('manualbook_id', 20);
            $table->time('time')->nullable();
            $table->string('name')->nullable();
            $table->string('pax')->nullable();
            $table->string('flight')->nullable();
            $table->string('orang')->nullable();
            $table->string('barang')->nullable();
            $table->text('temuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('manualbook_id')->references('id')->on('manualbooks')->onDelete('cascade');

            $table->index('manualbook_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manualbook_details');
        Schema::dropIfExists('manualbooks');
    }
};
