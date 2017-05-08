<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_notices', function (Blueprint $table) {
            $table->increments('id');

            $table->smallInteger('type');
            $table->smallInteger('operator');
            $table->smallInteger('status');
            $table->decimal('preset', 8, 2);
            $table->integer('price_id')->unique();
            $table->integer('action_id');
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
        Schema::dropIfExists('price_notices');
    }
}
