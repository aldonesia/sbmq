<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMProject extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_project', function (Blueprint $table) {
			$table->bigIncrements('proj_id');

			// foreign key
			$table->integer('user_id');

			$table->string('proj_name', 128)->index();
			$table->string('proj_building_no', 128);
			$table->string('proj_owner', 128)->index();
			$table->string('proj_workgroup', 128);
			$table->string('proj_weight_factor', 128);

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
		Schema::dropIfExists('m_project');
		/*Schema::table('m_project', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });*/
	}
}
