<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbPanel extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_panel', function (Blueprint $table) {
			$table->bigIncrements('pan_id');

			// foreign key
			$table->integer('proj_id')->unsigned();
			$table->integer('pt_id')->unsigned();
			$table->integer('ppos_id')->unsigned();
			$table->integer('block_id')->unsigned()->default(0);
			$table->integer('proc_id')->unsigned()->default(0);
			$table->integer('stat_id')->unsigned()->default(0);

			$table->integer('pan_no')->unsigned()->default(1);

			$table->string('remark', 128)->default('');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tb_panel');
	}
}
