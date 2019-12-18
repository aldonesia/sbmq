<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbBlock extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_block', function (Blueprint $table) {
			$table->bigIncrements('block_id');

			// foreign key
			$table->integer('proj_id')->unsigned();
			$table->integer('bt_id')->unsigned();
			$table->integer('ship_id')->unsigned()->default(0);
			$table->integer('proc_id')->unsigned()->default(0);
			$table->integer('stat_id')->unsigned()->default(0);

			$table->integer('block_no')->unsigned()->default(1);

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
		Schema::dropIfExists('tb_block');
	}
}
