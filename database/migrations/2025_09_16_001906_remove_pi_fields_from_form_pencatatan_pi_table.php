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
        Schema::table('form_pencatatan_pi', function (Blueprint $table) {
            if (Schema::hasColumn('form_pencatatan_pi', 'jenis_PI')) {
                $table->dropColumn('jenis_PI');
            }
            if (Schema::hasColumn('form_pencatatan_pi', 'in_quantity')) {
                $table->dropColumn('in_quantity');
            }
            if (Schema::hasColumn('form_pencatatan_pi', 'out_quantity')) {
                $table->dropColumn('out_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_pencatatan_pi', function (Blueprint $table) {
            $table->string('jenis_PI')->after('agency')->nullable();
            $table->string('in_quantity')->after('jenis_PI')->nullable();
            $table->string('out_quantity')->nullable()->after('in_quantity');
        });
    }
};