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
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            
            // Report identification
            $table->string('reportID', 50)->unique()->comment('Format: R-xxxxxx1');
            
            // Test information
            $table->date('testDate')->comment('Date when test was conducted');
            
            // Equipment and location
            $table->unsignedBigInteger('equipmentLocationID')->comment('Foreign key to equipment_locations table');
            
            // Device and certificate information (stored as JSON)
            $table->json('deviceInfo')->nullable()->comment('Device information in JSON format');
            $table->json('certificateInfo')->nullable()->comment('Certificate information in JSON format');
            
            // Test results
            $table->enum('result', ['pass', 'fail', 'pending', 'n/a'])->default('pending')->comment('Test result status');
            $table->text('note')->nullable()->comment('Additional notes for the report');
            
            // Status tracking
            $table->unsignedBigInteger('statusID')->comment('Foreign key to statuses table');
            
            // Submission information
            $table->unsignedBigInteger('submittedByID')->comment('Foreign key to users table - who submitted');
            $table->text('submitterSignature')->nullable()->comment('Digital signature of submitter');
            
            // Approval information
            $table->unsignedBigInteger('approvedByID')->nullable()->comment('Foreign key to users table - who approved');
            $table->text('approverSignature')->nullable()->comment('Digital signature of approver');
            $table->text('approvalNote')->nullable()->comment('Notes from approver');
            
            // Review information
            $table->unsignedBigInteger('reviewedByID')->nullable()->comment('Foreign key to users table - who reviewed');
            $table->timestamp('reviewed_at')->nullable()->comment('When the report was reviewed');
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('reportID');
            $table->index('testDate');
            $table->index('equipmentLocationID');
            $table->index('statusID');
            $table->index('submittedByID');
            $table->index('approvedByID');
            $table->index('reviewedByID');
            $table->index('result');
            $table->index('created_at');
            $table->index('deleted_at');
            
            // Composite indexes for common queries
            $table->index(['statusID', 'testDate']);
            $table->index(['submittedByID', 'created_at']);
            $table->index(['result', 'testDate']);
            
            // Foreign key constraints (uncomment if you want to enforce referential integrity)
            // Note: Make sure the referenced tables exist before uncommenting these
            /*
            $table->foreign('equipmentLocationID')
                  ->references('id')
                  ->on('equipment_locations')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreign('statusID')
                  ->references('id')
                  ->on('statuses')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreign('submittedByID')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreign('approvedByID')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreign('reviewedByID')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            */
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