<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSkillIdToMonstersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('monsters', function($table) {
            $table->integer('skill_id')->unsigned()->nullable();
            $table->foreign('skill_id')->references('id')->on('skills');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('monsters', function($table) {
            $table->dropColumn('skill_id');
        });
	}

}
