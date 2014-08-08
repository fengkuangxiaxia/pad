<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDungeonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dungeons', function($table) {
            $table->increments('id');
            $table->string('name');
		    $table->integer('level');
            $table->integer('father_id')->unsigned()->nullable();
            
            $table->foreign('father_id')->references('id')->on('dungeons')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('dungeons');
	}

}
