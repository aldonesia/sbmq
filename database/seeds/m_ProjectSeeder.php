<?php

use Illuminate\Database\Seeder;

use App\User;

class m_ProjectSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$date= date("Y-m-d H:i:s", mt_rand(strtotime("2017-09-01"),strtotime("2017-09-31")));
		DB::table('m_project')->insert([
			[
				'user_id' => 2,
				'proj_name' => 'Tag Boat',
				'proj_building_no' => '1001',
				'proj_owner' => 'Cigading Port',
				'proj_workgroup' => 'Krakatau Steel',
				'proj_weight_factor' => '10',
				'created_at' => $date,
			],
		]);

		DB::table('tb_progress')->insert([
			[
				'proj_id' => 1,
				'user_id' => 2,
				'proc_id' => 1,
				'prog_remark' => 'Project Tag Boat was created by PPC 1',
				'created_at' => $date,
			],
		]);
	}
}
