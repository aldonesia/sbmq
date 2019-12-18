<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbMaterial extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_material', function (Blueprint $table) {
			$table->bigIncrements('mat_id');

			// foreign key
			$table->integer('proj_id')->unsigned();
			$table->integer('mt_id')->unsigned();
			$table->integer('pp_proc_id')->unsigned()->default(0);
			$table->integer('pr_proc_id')->unsigned()->default(2);
			$table->integer('pp_stat_id')->unsigned()->default(0);
			$table->integer('pr_stat_id')->unsigned()->default(0);

			// content
			$table->integer('mat_no')->unsigned()->default(1);
			$table->string('mat_spec', 128);
			$table->float('mat_thick', 8, 2);

			$table->timestamp('purchased_at')->nullable();
			$table->timestamp('arrived_at')->nullable();

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
		Schema::dropIfExists('tb_material');
	}
}
