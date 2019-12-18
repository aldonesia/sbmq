<?php

use Illuminate\Database\Seeder;

class m_BlockTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('m_block_type')->insert([
			[
				'bt_name' => 'Top Deck',
				'bt_shortname' => 'TD',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Navigation Deck',
				'bt_shortname' => 'ND',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Bridge Deck',
				'bt_shortname' => 'BD',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Boat Deck',
				'bt_shortname' => 'BD',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Poop Deck',
				'bt_shortname' => 'PD',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Aft Deck',
				'bt_shortname' => 'AD',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Engine Room',
				'bt_shortname' => 'ER',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Cargo Hold',
				'bt_shortname' => 'CH',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Fore Peak',
				'bt_shortname' => 'FP',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Forecastle Deck',
				'bt_shortname' => 'FD',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Super Structure',
				'bt_shortname' => 'SS',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Funnel',
				'bt_shortname' => 'FU',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Skeg',
				'bt_shortname' => 'SK',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'bt_name' => 'Communication',
				'bt_shortname' => 'CO',
				'created_at' => date("Y-m-d H:i:s"),
			],
		]);
	}
}
