<?php

use Illuminate\Database\Seeder;

class tb_PanelSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('tb_panel')->insert([
			[
				'proj_id' => 1,
				'pt_id'	=> 1,
				'ppos_id'	=> 1,
				'block_id'	=> 1,
				'proc_id' => 17,
				'stat_id' => 4,
				'pan_no'	=> 1,
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'pt_id'	=> 2,
				'ppos_id'	=> 2,
				'block_id'	=> 1,
				'proc_id' => 17,
				'stat_id' => 4,
				'pan_no'	=> 2,
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'pt_id'	=> 3,
				'ppos_id'	=> 3,
				'block_id'	=> 2,
				'proc_id' => 17,
				'stat_id' => 4,
				'pan_no'	=> 3,
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'pt_id'	=> 4,
				'ppos_id'	=> 2,
				'block_id'	=> 2,
				'proc_id' => 17,
				'stat_id' => 4,
				'pan_no'	=> 5,
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
		]);

		// PROGREESS BLOCK PROJECT 1
		DB::table('tb_progress')->insert([
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 14,
				'pan_id'	=> 1,
				'prog_remark' => 'item moved to sub-assembly process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 14,
				'pan_id'	=> 2,
				'prog_remark' => 'item moved to sub-assembly process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 14,
				'pan_id'	=> 3,
				'prog_remark' => 'item moved to sub-assembly process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 14,
				'pan_id'	=> 4,
				'prog_remark' => 'item moved to sub-assembly process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 15,
				'pan_id'	=> 1,
				'prog_remark' => 'item on sub-assembly - fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 15,
				'pan_id'	=> 2,
				'prog_remark' => 'item on sub-assembly - fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 15,
				'pan_id'	=> 3,
				'prog_remark' => 'item on sub-assembly - fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 15,
				'pan_id'	=> 4,
				'prog_remark' => 'item on sub-assembly - fitting process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 16,
				'pan_id'	=> 1,
				'prog_remark' => 'item on sub-assembly - welding process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 16,
				'pan_id'	=> 2,
				'prog_remark' => 'item on sub-assembly - welding process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 16,
				'pan_id'	=> 3,
				'prog_remark' => 'item on sub-assembly - welding process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 16,
				'pan_id'	=> 4,
				'prog_remark' => 'item on sub-assembly - welding process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-07-01"),strtotime("2018-08-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 17,
				'pan_id'	=> 1,
				'prog_remark' => 'item on sub-assembly - fairing process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-08-01"),strtotime("2018-09-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 17,
				'pan_id'	=> 2,
				'prog_remark' => 'item on sub-assembly - fairing process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-08-01"),strtotime("2018-09-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 17,
				'pan_id'	=> 3,
				'prog_remark' => 'item on sub-assembly - fairing process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-08-01"),strtotime("2018-09-13"))),
			],
			[
				'proj_id' => 1,
				'user_id' => 1,
				'proc_id' => 17,
				'pan_id'	=> 4,
				'prog_remark' => 'item on sub-assembly - fairing process at ' . date("d-m-Y H:i:s"),
				'created_at' => date("Y-m-d H:i:s", mt_rand(strtotime("2018-08-01"),strtotime("2018-09-13"))),
			],
		]);
	}
}
