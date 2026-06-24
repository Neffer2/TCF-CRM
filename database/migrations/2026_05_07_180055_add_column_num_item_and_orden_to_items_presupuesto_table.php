<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNumItemAndOrdenToItemsPresupuestoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items_presupuesto', function (Blueprint $table) {
            $table->integer('num_item')->nullable()->after('presupuesto_id');
            $table->integer('orden')->nullable()->after('num_item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items_presupuesto', function (Blueprint $table) {
            $table->dropColumn('num_item');
            $table->dropColumn('orden');
        });
    }
}
