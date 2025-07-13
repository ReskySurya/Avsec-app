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
        Schema::create('reports', function (Blueprint $table) {
            $table->string('reportID', 20)->primary();
            $table->datetime('testDate');
            
            // Foreign key to equipment_locations table
            $table->unsignedBigInteger('equipmentLocationID');
            $table->foreign('equipmentLocationID')->references('id')->on('equipment_locations')->onDelete('cascade');
            $table->text('deviceInfo')->nullable();
            $table->text('certificateInfo')->nullable();
            $table->boolean('isFullFilled')->default(false);
            $table->text('result')->nullable();
            $table->text('note')->nullable();
            
            // Foreign key to report_statuses table
            $table->unsignedBigInteger('statusID');
            $table->foreign('statusID')->references('id')->on('report_statuses')->onDelete('cascade');
            
            // Submitted by (Foreign key to users table)
            $table->unsignedBigInteger('submittedByID');
            $table->foreign('submittedByID')->references('id')->on('users')->onDelete('cascade');
            $table->text('submitterSignature')->nullable();
            
            // Approved by (Foreign key to users table)
            $table->unsignedBigInteger('approvedByID')->nullable();
            $table->foreign('approvedByID')->references('id')->on('users')->onDelete('set null');
            $table->text('approverSignature')->nullable();
            $table->text('approvalNote')->nullable();
            
            // Reviewed by (Foreign key to users table)
            $table->unsignedBigInteger('reviewedByID')->nullable();
            $table->foreign('reviewedByID')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['testDate']);
            $table->index(['statusID']);
            $table->index(['submittedByID']);
            $table->index(['created_at']);
            $table->index(['equipmentLocationID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};