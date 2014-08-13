<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersMonstersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_monsters', function($table) {
            //$table->increments('id');
            
            $table->integer('user_id')->unsigned();
		    $table->integer('monster_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('monster_id')->references('id')->on('monsters')->onDelete('cascade');
            
            $table->primary(array('user_id','monster_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users_monsters');
	}

}
