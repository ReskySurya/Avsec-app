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
        Schema::create('report_details', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke reports table - menggunakan string karena reportID adalah string
            $table->string('reportID', 20);
            $table->foreign('reportID')->references('reportID')->on('reports')->onDelete('cascade');
            
            $table->integer('terpenuhi')->default(0);
            $table->integer('tidakTerpenuhi')->default(0);
            
            //wtmd fields
            $table->tinyInteger('test1_in_depan')->nullable();
            $table->tinyInteger('test1_out_depan')->nullable();
            $table->tinyInteger('test2_in_depan')->nullable();
            $table->tinyInteger('test2_out_depan')->nullable();
            $table->tinyInteger('test3_in_depan')->nullable();
            $table->tinyInteger('test3_out_depan')->nullable();
            $table->tinyInteger('test4_in_depan')->nullable();
            $table->tinyInteger('test4_out_depan')->nullable();

            //whmd fields
            $table->tinyInteger('test1')->nullable();
            $table->tinyInteger('test2')->nullable();
            $table->tinyInteger('test3')->nullable();
            $table->tinyInteger('testCondition1')->nullable();
            $table->tinyInteger('testCondition2')->nullable();

            //xray fields
            $table->tinyInteger('test1ab_36')->nullable();
            $table->tinyInteger('test1ab_32')->nullable();
            $table->tinyInteger('test1ab_30')->nullable();
            $table->tinyInteger('test1ab_24')->nullable();
            $table->tinyInteger('test1bb_36_1')->nullable();
            $table->tinyInteger('test1bb_32_1')->nullable();
            $table->tinyInteger('test1bb_30_1')->nullable();
            $table->tinyInteger('test1bb_24_1')->nullable();
            $table->tinyInteger('test1bb_36_2')->nullable();
            $table->tinyInteger('test1bb_32_2')->nullable();
            $table->tinyInteger('test1bb_30_2')->nullable();
            $table->tinyInteger('test1bb_24_2')->nullable();
            $table->tinyInteger('test1bb_36_3')->nullable();
            $table->tinyInteger('test1bb_32_3')->nullable();
            $table->tinyInteger('test1bb_30_3')->nullable();
            $table->tinyInteger('test1bb_24_3')->nullable();
            $table->tinyInteger('test3ab_14')->nullable();
            $table->tinyInteger('test3ab_16')->nullable();
            $table->tinyInteger('test3ab_18')->nullable();
            $table->tinyInteger('test3ab_20')->nullable();
            $table->tinyInteger('test3ab_22')->nullable();
            $table->tinyInteger('test3ab_24')->nullable();
            $table->tinyInteger('test3ab_26')->nullable();
            $table->tinyInteger('test3ab_28')->nullable();
            $table->tinyInteger('test3ab_30')->nullable();
            $table->tinyInteger('test4ab_h10mm')->nullable();
            $table->tinyInteger('test4ab_v10mm')->nullable();
            $table->tinyInteger('test4ab_h15mm')->nullable();
            $table->tinyInteger('test4ab_v15mm')->nullable();
            $table->tinyInteger('test4ab_h20mm')->nullable();
            $table->tinyInteger('test4ab_v20mm')->nullable();
            $table->tinyInteger('test5ab_05mm')->nullable();
            $table->tinyInteger('test5ab_10mm')->nullable();
            $table->tinyInteger('test5ab_15mm')->nullable();
            $table->tinyInteger('test2ab')->nullable();
            $table->tinyInteger('test2bb')->nullable();
            $table->tinyInteger('test3b_14')->nullable();
            $table->tinyInteger('test3b_16')->nullable();
            $table->tinyInteger('test3b_18')->nullable();
            $table->tinyInteger('test3b_20')->nullable();
            $table->tinyInteger('test3b_22')->nullable();
            $table->tinyInteger('test3b_24')->nullable();
            $table->tinyInteger('test3b_26')->nullable();
            $table->tinyInteger('test3b_28')->nullable();
            $table->tinyInteger('test3b_30')->nullable();
            $table->tinyInteger('test4b_h10mm')->nullable();
            $table->tinyInteger('test4b_v10mm')->nullable();
            $table->tinyInteger('test4b_h15mm')->nullable();
            $table->tinyInteger('test4b_v15mm')->nullable();
            $table->tinyInteger('test4b_h20mm')->nullable();
            $table->tinyInteger('test4b_v20mm')->nullable();
            $table->tinyInteger('test5b_05mm')->nullable();
            $table->tinyInteger('test5b_10mm')->nullable();
            $table->tinyInteger('test5b_15mm')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['reportID']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_details');
    }
};