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
            $table->foreignId('reportID');
            $table->integer('terpenuhi');
            $table->integer('tidakTerpenuhi');
            
            //wtmd
            $table->tinyInteger('test1_in_depan');
            $table->tinyInteger('test1_out_depan');
            $table->tinyInteger('test2_in_depan');
            $table->tinyInteger('test2_out_depan');
            $table->tinyInteger('test3_in_depan');
            $table->tinyInteger('test3_out_depan');
            $table->tinyInteger('test4_in_depan');
            $table->tinyInteger('test4_out_depan');

            //whmd
            $table->tinyInteger('test1');
            $table->tinyInteger('test2');
            $table->tinyInteger('test3');
            $table->tinyInteger('testCondition1');
            $table->tinyInteger('testCondition2');

            //xray
            $table->tinyInteger('test1ab_36');
            $table->tinyInteger('test1ab_32');
            $table->tinyInteger('test1ab_30');
            $table->tinyInteger('test1ab_24');
            $table->tinyInteger('test1bb_36_1');
            $table->tinyInteger('test1bb_32_1');
            $table->tinyInteger('test1bb_30_1');
            $table->tinyInteger('test1bb_24_1');
            $table->tinyInteger('test1bb_36_2');
            $table->tinyInteger('test1bb_32_2');
            $table->tinyInteger('test1bb_30_2');
            $table->tinyInteger('test1bb_24_2');
            $table->tinyInteger('test1bb_36_3');
            $table->tinyInteger('test1bb_32_3');
            $table->tinyInteger('test1bb_30_3');
            $table->tinyInteger('test1bb_24_3');
            $table->tinyInteger('test3ab_14');
            $table->tinyInteger('test3ab_16');
            $table->tinyInteger('test3ab_18');
            $table->tinyInteger('test3ab_20');
            $table->tinyInteger('test3ab_22');
            $table->tinyInteger('test3ab_24');
            $table->tinyInteger('test3ab_26');
            $table->tinyInteger('test3ab_28');
            $table->tinyInteger('test3ab_30');
            $table->tinyInteger('test4ab_h10mm');
            $table->tinyInteger('test4ab_v10mm');
            $table->tinyInteger('test4ab_h15mm');
            $table->tinyInteger('test4ab_v15mm');
            $table->tinyInteger('test4ab_h20mm');
            $table->tinyInteger('test4ab_v20mm');
            $table->tinyInteger('test5ab_05mm');
            $table->tinyInteger('test5ab_10mm');
            $table->tinyInteger('test5ab_15mm');
            $table->tinyInteger('test2ab');
            $table->tinyInteger('test2bb');
            $table->tinyInteger('test3b_14');
            $table->tinyInteger('test3b_16');
            $table->tinyInteger('test3b_18');
            $table->tinyInteger('test3b_20');
            $table->tinyInteger('test3b_22');
            $table->tinyInteger('test3b_24');
            $table->tinyInteger('test3b_26');
            $table->tinyInteger('test3b_28');
            $table->tinyInteger('test3b_30');
            $table->tinyInteger('test4b_h10mm');
            $table->tinyInteger('test4b_v10mm');
            $table->tinyInteger('test4b_h15mm');
            $table->tinyInteger('test4b_v15mm');
            $table->tinyInteger('test4b_h20mm');
            $table->tinyInteger('test4b_v20mm');
            $table->tinyInteger('test5b_05mm');
            $table->tinyInteger('test5b_10mm');
            $table->tinyInteger('test5b_15mm');
            $table->timestamps();
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
