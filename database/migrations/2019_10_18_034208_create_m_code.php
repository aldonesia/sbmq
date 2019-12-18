<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMCode extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_code', function (Blueprint $table) {
			$table->bigIncrements('code_id');
			$table->string('code_name', 19)->unique()->index();
			$table->integer('code_stat')->default(0);
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
		Schema::dropIfExists('m_code');
	}
}
