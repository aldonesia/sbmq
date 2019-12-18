<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMMaterialType extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_material_type', function (Blueprint $table) {
			$table->bigIncrements('mt_id');

			// parental nemonic
			$table->string('mt_mne', 128)->index();
			$table->integer('mt_parid');
			$table->integer('mt_lvl');
			$table->integer('mt_seq');

			$table->string('mt_name', 128);

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
		Schema::dropIfExists('m_material_type');
	}
}
