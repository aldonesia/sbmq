<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbShip extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_ship', function (Blueprint $table) {
			$table->bigIncrements('ship_id');

			// foreign key
			$table->integer('proj_id')->unsigned();
			$table->integer('proc_id')->unsigned()->default(0);
			$table->integer('stat_id')->unsigned()->default(0);

			$table->timestamp('delivered_at')->nullable();

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
		Schema::dropIfExists('tb_ship');
	}
}
