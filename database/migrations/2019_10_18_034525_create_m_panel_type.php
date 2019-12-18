<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMPanelType extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_panel_type', function (Blueprint $table) {
			$table->bigIncrements('pt_id');
			$table->string('pt_name', 100);
			$table->string('pt_shortname', 2);
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
		Schema::dropIfExists('m_panel_type');
	}
}
