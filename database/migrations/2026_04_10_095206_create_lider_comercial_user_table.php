<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiderComercialUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lider_comercial_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('comercial_id')->constrained('users')->cascadeOnDelete();

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
        Schema::dropIfExists('lider_comercial_user');
    }
}
