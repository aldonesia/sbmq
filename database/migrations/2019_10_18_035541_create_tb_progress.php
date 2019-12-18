<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbProgress extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_progress', function (Blueprint $table) {
			$table->bigIncrements('prog_id');

			// foreign key
			$table->integer('proj_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->integer('proc_id')->unsigned();

			$table->integer('mat_id')->unsigned()->default(0);
			$table->integer('pp_id')->unsigned()->default(0);
			$table->integer('pan_id')->unsigned()->default(0);
			$table->integer('block_id')->unsigned()->default(0);
			$table->integer('ship_id')->unsigned()->default(0);

			$table->integer('code_id')->unsigned()->default(0);

			$table->string('prog_remark', 512)->default('');

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
		Schema::dropIfExists('tb_progress');
	}
}
