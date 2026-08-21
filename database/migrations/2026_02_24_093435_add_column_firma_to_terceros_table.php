<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFirmaToTercerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->string('firma')->nullable()->after('planilla_aportes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->dropColumn('firma');
        });
    }
}
