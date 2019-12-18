<?php

use Illuminate\Database\Seeder;

class m_StatusSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('m_stat')->insert([
			[
				'stat_name' => 'Waiting for the next sub-process',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'stat_name' => 'Waiting for the next process',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'stat_name' => 'Has been used in the next processs',
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'stat_name' => 'All Completed',
				'created_at' => date("Y-m-d H:i:s"),
			],
		]);
	}
}
