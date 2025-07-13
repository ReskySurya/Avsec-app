<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDetail extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected $table = 'report_details';
    //
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'reportID',
        'terpenuhi',
        'tidakTerpenuhi',

        //wtmd
        'test1_in_depan',
        'test1_out_depan',
        'test2_in_depan',
        'test2_out_depan',
        'test3_in_belakang',
        'test3_out_belakang',
        'test4_in_depan',
        'test4_out_depan',

        //hhmd
        'test1',
        'testCondition1',
        'testCondition2',

        //xray
        'test1ab_36',
        'test1ab_32',
        'test1ab_30',
        'test1ab_24',
        'test1bb_36_1',
        'test1bb_32_1',
        'test1bb_30_1',
        'test1bb_24_1',
        'test1bb_36_2',
        'test1bb_32_2',
        'test1bb_30_2',
        'test1bb_24_2',
        'test1bb_36_3',
        'test1bb_32_3',
        'test1bb_30_3',
        'test1bb_24_3',
        'test3ab_14',
        'test3ab_16',
        'test3ab_18',
        'test3ab_20',
        'test3ab_22',
        'test3ab_24',
        'test3ab_26',
        'test3ab_28',
        'test3ab_30',
        'test4ab_h10mm',
        'test4ab_v10mm',
        'test4ab_h15mm',
        'test4ab_v15mm',
        'test4ab_h20mm',
        'test4ab_v20mm',
        'test5ab_05mm',
        'test5ab_10mm',
        'test5ab_15mm',
        'test2aab',
        'test2bab',
        'test2ab',
        'test2bb',
        'test3b_14',
        'test3b_16',
        'test3b_18',
        'test3b_20',
        'test3b_22',
        'test3b_24',
        'test3b_26',
        'test3b_28',
        'test3b_30',
        'test4b_h10mm',
        'test4b_v10mm',
        'test4b_h15mm',
        'test4b_v15mm',
        'test4b_h20mm',
        'test4b_v20mm',
        'test5b_05mm',
        'test5b_10mm',
        'test5b_15mm'
    ];
    /**
     * The primary key associated with the table.
     */

}
