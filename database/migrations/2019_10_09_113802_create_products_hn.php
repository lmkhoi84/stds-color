<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsHn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('products_hn', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->string('product_url',200);
            $table->bigInteger('parent_id')->index()->unsigned();
            $table->foreign('parent_id')->references('id')->on('structure')->onUpdate('cascade');
            $table->string('sort',100);
            $table->string('position',100);
            $table->string('sale_off_type',100);
            $table->tinyInteger('status');
            $table->tinyInteger('show_in_stock');
            $table->tinyInteger('calculate_type');
            $table->string('actual_quantity',200);
            $table->double('2012');
            $table->double('2013');
            $table->double('2014');
            $table->double('2015');
            $table->double('2016');
            $table->double('2017');
            $table->double('2018');
            $table->double('2019');
            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_hn');
    }
}
