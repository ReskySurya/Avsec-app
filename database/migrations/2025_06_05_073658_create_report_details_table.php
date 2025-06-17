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

            //hhmd fields
            $table->tinyInteger('test1')->nullable();
            $table->tinyInteger('testCondition1')->nullable();
            $table->tinyInteger('testCondition2')->nullable();

            //wtmd fields
            $table->tinyInteger('test1_in_depan')->nullable();
            $table->tinyInteger('test1_out_depan')->nullable();
            $table->tinyInteger('test2_in_depan')->nullable();
            $table->tinyInteger('test2_out_depan')->nullable();
            $table->tinyInteger('test3_in_belakang')->nullable();
            $table->tinyInteger('test3_out_belakang')->nullable();
            $table->tinyInteger('test4_in_depan')->nullable();
            $table->tinyInteger('test4_out_depan')->nullable();

            //xray fields
            // Test 1a dan 1b
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
            $table->tinyInteger('test1aab_36')->nullable();
            $table->tinyInteger('test1aab_32')->nullable();
            $table->tinyInteger('test1aab_30')->nullable();
            $table->tinyInteger('test1aab_24')->nullable();
            $table->tinyInteger('test1bab_36_1')->nullable();
            $table->tinyInteger('test1bab_32_1')->nullable();
            $table->tinyInteger('test1bab_30_1')->nullable();
            $table->tinyInteger('test1bab_24_1')->nullable();
            $table->tinyInteger('test1bab_36_2')->nullable();
            $table->tinyInteger('test1bab_32_2')->nullable();
            $table->tinyInteger('test1bab_30_2')->nullable();
            $table->tinyInteger('test1bab_24_2')->nullable();
            $table->tinyInteger('test1bab_36_3')->nullable();
            $table->tinyInteger('test1bab_32_3')->nullable();
            $table->tinyInteger('test1bab_30_3')->nullable();
            $table->tinyInteger('test1bab_24_3')->nullable();

            // Test 2a dan 2b
            $table->tinyInteger('test2ab')->nullable();
            $table->tinyInteger('test2bb')->nullable();
            $table->tinyInteger('test2aab')->nullable();
            $table->tinyInteger('test2bab')->nullable();

            // Test 3a dan 3b
            $table->tinyInteger('test3ab_14')->nullable();
            $table->tinyInteger('test3ab_16')->nullable();
            $table->tinyInteger('test3ab_18')->nullable();
            $table->tinyInteger('test3ab_20')->nullable();
            $table->tinyInteger('test3ab_22')->nullable();
            $table->tinyInteger('test3ab_24')->nullable();
            $table->tinyInteger('test3ab_26')->nullable();
            $table->tinyInteger('test3ab_28')->nullable();
            $table->tinyInteger('test3ab_30')->nullable();
            $table->tinyInteger('test3b_14')->nullable();
            $table->tinyInteger('test3b_16')->nullable();
            $table->tinyInteger('test3b_18')->nullable();
            $table->tinyInteger('test3b_20')->nullable();
            $table->tinyInteger('test3b_22')->nullable();
            $table->tinyInteger('test3b_24')->nullable();
            $table->tinyInteger('test3b_26')->nullable();
            $table->tinyInteger('test3b_28')->nullable();
            $table->tinyInteger('test3b_30')->nullable();

            // Test 4a dan 4b
            $table->tinyInteger('test4ab_h10mm')->nullable();
            $table->tinyInteger('test4ab_v10mm')->nullable();
            $table->tinyInteger('test4ab_h15mm')->nullable();
            $table->tinyInteger('test4ab_v15mm')->nullable();
            $table->tinyInteger('test4ab_h20mm')->nullable();
            $table->tinyInteger('test4ab_v20mm')->nullable();
            $table->tinyInteger('test4b_h10mm')->nullable();
            $table->tinyInteger('test4b_v10mm')->nullable();
            $table->tinyInteger('test4b_h15mm')->nullable();
            $table->tinyInteger('test4b_v15mm')->nullable();
            $table->tinyInteger('test4b_h20mm')->nullable();
            $table->tinyInteger('test4b_v20mm')->nullable();

            // Test 5a dan 5b
            $table->tinyInteger('test5ab_05mm')->nullable();
            $table->tinyInteger('test5ab_10mm')->nullable();
            $table->tinyInteger('test5ab_15mm')->nullable();
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
