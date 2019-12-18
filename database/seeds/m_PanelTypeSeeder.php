<?php

use Illuminate\Database\Seeder;

class m_PanelTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('m_panel_type')->insert([
			[
				'pt_name' => 'Side',
				'pt_shortname' => 'SD',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'pt_name' => 'Bottom',
				'pt_shortname' => 'BT',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'pt_name' => 'Double Bottom',
				'pt_shortname' => 'DB',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'pt_name' => 'Deck',
				'pt_shortname' => 'DK',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'pt_name' => 'Tween Deck',
				'pt_shortname' => 'TD',
				'created_at' => date("Y-m-d H:i:s"),
			],
		]);
	}
}
