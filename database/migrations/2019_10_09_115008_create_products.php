<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->string('product_url',200);
            $table->bigInteger('parent_id')->index()->unsigned();
            $table->foreign('parent_id')->references('id')->on('structure')->onUpdate('cascade');
            $table->string('sort',100);
            $table->string('sale_off_type',100);
            $table->tinyInteger('status');
            $table->tinyInteger('show_in_stock');
            $table->tinyInteger('calculate_type');
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
        Schema::dropIfExists('products');
    }
}
