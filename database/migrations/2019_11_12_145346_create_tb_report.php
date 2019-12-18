<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbReport extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_report', function (Blueprint $table) {
			$table->bigIncrements('report_id');
			$table->integer('proj_id')->unsigned()->default(0);
			$table->float('report_plan',8,2)->default(0);
			$table->integer('report_month')->unsigned()->default(0);
			$table->integer('report_year')->unsigned()->default(0);
			$table->string('remark', 128)->default('');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tb_report');
	}
}
