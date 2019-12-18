<?php

use Illuminate\Database\Seeder;

class m_PanelPositionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('m_panel_position')->insert([
			[
				'ppos_name' => 'Portside',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'ppos_name' => 'Starboard',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'ppos_name' => 'Center',
				'created_at' => date("Y-m-d H:i:s"),
			],
		]);
	}
}
