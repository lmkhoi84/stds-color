<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('structure', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->string('structure_url',100)->index();
            $table->integer('parent_id')->index()->nullable();
            $table->tinyInteger('page_type')->index();
            $table->integer('sort');
            $table->integer('level');
            $table->tinyInteger('status')->index();
            $table->string('icon',100);
            $table->integer('created_user');
            $table->timestamps();
        });

        Schema::create('structure_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->bigInteger('structure_id')->unsigned();
            $table->string('structure_name');
            $table->text('trans_page');
            $table->string('locale')->index();
            $table->unique(['structure_id', 'locale']);
            $table->foreign('structure_id')->references('id')->on('structure')->onDelete('cascade');
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
        Schema::dropIfExists('structure');
        Schema::dropIfExists('structure_translations');
    }
}
