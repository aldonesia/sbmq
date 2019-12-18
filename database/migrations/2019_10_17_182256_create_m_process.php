<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMProcess extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_process', function (Blueprint $table) {
			$table->bigIncrements('proc_id');

			// parental nemonic
			$table->string('proc_mne', 128)->index();
			$table->integer('proc_parid')->unsigned()->default(0);
			$table->integer('proc_lvl')->unsigned();
			$table->integer('proc_seq')->unsigned();

			// content
			$table->string('proc_name', 128);
			$table->string('proc_shortname', 2)->default('');
			$table->float('proc_score',8,2)->default(0);

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
		Schema::dropIfExists('m_process');
	}
}
