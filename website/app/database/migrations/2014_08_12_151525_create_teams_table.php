<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('teams', function($table) {
            $table->increments('id');
            
            $table->integer('leader_id')->unsigned();
            $table->integer('monster1_id')->unsigned()->nullable();
            $table->integer('monster2_id')->unsigned()->nullable();
            $table->integer('monster3_id')->unsigned()->nullable();
            $table->integer('monster4_id')->unsigned()->nullable();
            $table->integer('friend_id')->unsigned()->nullable();
            
            $table->foreign('leader_id')->references('id')->on('monsters')->onDelete('cascade');
            $table->foreign('monster1_id')->references('id')->on('monsters')->onDelete('cascade');
            $table->foreign('monster2_id')->references('id')->on('monsters')->onDelete('cascade');
            $table->foreign('monster3_id')->references('id')->on('monsters')->onDelete('cascade');
            $table->foreign('monster4_id')->references('id')->on('monsters')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('monsters')->onDelete('cascade');
            
            $table->integer('dungeon_id')->unsigned();
            $table->foreign('dungeon_id')->references('id')->on('dungeons')->onDelete('cascade');
            
            $table->integer('hp');
		    $table->integer('stone');
		    $table->text('description');
            
            $table->unique(array('leader_id','monster1_id','monster2_id','monster3_id','monster4_id','friend_id','dungeon_id','hp','stone'), 'unique_index');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('teams');
	}

}
