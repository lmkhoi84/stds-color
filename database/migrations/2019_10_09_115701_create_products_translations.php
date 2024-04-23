<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('products_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned();
            $table->string('product_name');
            $table->string('product_unit');
            $table->string('locale')->index();

            $table->unique(['product_id', 'locale']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('products_translations');
    }
}
