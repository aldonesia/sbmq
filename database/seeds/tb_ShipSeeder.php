<?php

use Illuminate\Database\Seeder;

class tb_ShipSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('tb_ship')->insert([
			[
				'proj_id' => 1,
				'proc_id' => 28,
				'stat_id' => 4,
				'delivered_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-02-11"),strtotime("2019-02-27"))),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-01-01"),strtotime("2019-01-17"))),
			],
		]);

		// progress project 1
		DB::table('tb_progress')->insert([
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 22,
				'ship_id'   => 1,
				'prog_remark' => 'item moved to erection process at ' . date("d-m-Y H:i:s"),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-01-01"),strtotime("2019-01-17"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 23,
				'ship_id'   => 1,
				'prog_remark' => 'item on erection - cutting to fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-01-01"),strtotime("2019-01-17"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 24,
				'ship_id'   => 1,
				'prog_remark' => 'item on erection - fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-01-01"),strtotime("2019-01-17"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 25,
				'ship_id'   => 1,
				'prog_remark' => 'item on erection - welding process at ' . date("d-m-Y H:i:s"),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-01-11"),strtotime("2019-01-27"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 26,
				'ship_id'   => 1,
				'prog_remark' => 'item on erection - final inspection process at ' . date("d-m-Y H:i:s"),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-02-01"),strtotime("2019-02-17"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 27,
				'ship_id'   => 1,
				'prog_remark' => 'item on erection - delivered process at ' . date("d-m-Y H:i:s"),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-02-11"),strtotime("2019-02-27"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 28,
				'ship_id'   => 1,
				'prog_remark' => 'item completed all process at ' . date("d-m-Y H:i:s"),
				'created_at' =>date("Y-m-d H:i:s", mt_rand(strtotime("2019-02-11"),strtotime("2019-02-27"))),
			],
		]);
	}
}
