<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbPiecepart extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_piecepart', function (Blueprint $table) {
			$table->bigIncrements('pp_id');

			// foreign key
			$table->integer('proj_id')->unsigned();
			$table->integer('mat_id')->unsigned();
			$table->integer('pan_id')->unsigned()->default(0);
			$table->integer('proc_id')->unsigned()->default(0);
			$table->integer('stat_id')->unsigned()->default(0);

			$table->string('pp_name', 512)->default('');
			$table->integer('pp_no')->unsigned()->default(1);

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
		Schema::dropIfExists('tb_piecepart');
	}
}
