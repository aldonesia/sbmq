<?php

use Illuminate\Database\Seeder;

class tb_BlockSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('tb_block')->insert([
			[
				'proj_id' => 1,
				'bt_id'	=> 1,
				'ship_id'=>1,
				'proc_id' => 21,
				'stat_id' => 4,
				'block_no'=> 1,
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-09-14"),strtotime("2018-10-30"))),
			],
			[
				'proj_id' => 1,
				'bt_id'	=> 2,
				'ship_id'=>1,
				'proc_id' => 21,
				'stat_id' => 4,
				'block_no'=> 2,
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-09-14"),strtotime("2018-10-30"))),
			],
		]);

		// PROGREESS BLOCK PROJECT 1
		DB::table('tb_progress')->insert([
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 18,
				'block_id'=> 1,
				'prog_remark' => 'item moved to assembly process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-09-14"),strtotime("2018-10-30"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 18,
				'block_id'=> 2,
				'prog_remark' => 'item moved to assembly process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-09-14"),strtotime("2018-10-30"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 19,
				'block_id'=> 1,
				'prog_remark' => 'item on assembly - cutting to fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-09-14"),strtotime("2018-10-30"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 19,
				'block_id'=> 2,
				'prog_remark' => 'item on assembly - cutting to fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-09-14"),strtotime("2018-10-30"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 20,
				'block_id'=> 1,
				'prog_remark' => 'item on assembly - fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-10-01"),strtotime("2018-11-30"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 20,
				'block_id'=> 2,
				'prog_remark' => 'item on assembly - fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-10-01"),strtotime("2018-11-30"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 21,
				'block_id'=> 1,
				'prog_remark' => 'item on assembly - welding process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-11-01"),strtotime("2018-12-30"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 21,
				'block_id'=> 2,
				'prog_remark' => 'item on assembly - welding process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-11-01"),strtotime("2018-12-30"))),
			],
		]);
	}
}
