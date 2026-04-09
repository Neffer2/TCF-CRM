<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsAnticipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_anticipo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anticipo_id')->constrained('anticipos');
            $table->foreignId('item_id')->constrained('items_presupuesto');

            $table->string('display_item');
            $table->string('desc');

            $table->integer('cant');
            $table->integer('dias');
            $table->integer('otros');

            $table->decimal('vunit', 15, 2)
                ->default(0.00);
            $table->decimal('vtotal', 15, 2)
                ->default(0.00);
            $table->decimal('vanticipo', 15, 2)
                ->default(0.00);
            $table->decimal('saldo', 15, 2)
                ->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items_anticipo');
    }
}
