<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('import', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            //$table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->string('date',10)->index()->unsigned();
            $table->bigInteger('salesman_id')->index()->unsigned();
            $table->bigInteger('supplier_id')->index()->unsigned();
            $table->string('number')->index()->unsigned();
            $table->tinyInteger('type');
            $table->integer('items');
            $table->tinyInteger('warehouse')->index()->unsigned();
            $table->string('note',255);
            $table->float('amount',11,4);
            $table->integer('created_user');
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
        Schema::dropIfExists('import');
    }
}
