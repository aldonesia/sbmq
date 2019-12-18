<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tb_ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('tb_report')->insert([
			[
				'report_month' 	=> 9,
				'report_year' 	=> 2017,
				'report_plan' 	=> 0,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 10,
				'report_year' 	=> 2017,
				'report_plan' 	=> 1.2,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 11,
				'report_year' 	=> 2017,
				'report_plan' 	=> 2.6,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 12,
				'report_year' 	=> 2017,
				'report_plan' 	=> 3.7,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 1,
				'report_year' 	=> 2018,
				'report_plan' 	=> 3.8,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 2,
				'report_year' 	=> 2018,
				'report_plan' 	=> 5.0,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 3,
				'report_year' 	=> 2018,
				'report_plan' 	=> 5.1,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 4,
				'report_year' 	=> 2018,
				'report_plan' 	=> 6.7,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 5,
				'report_year' 	=> 2018,
				'report_plan' 	=> 7.5,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 6,
				'report_year' 	=> 2018,
				'report_plan' 	=> 8.9,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 7,
				'report_year' 	=> 2018,
				'report_plan' 	=> 10.3,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 8,
				'report_year' 	=> 2018,
				'report_plan' 	=> 10.8,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 9,
				'report_year' 	=> 2018,
				'report_plan' 	=> 10.8,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 10,
				'report_year' 	=> 2018,
				'report_plan' 	=> 9.4,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 11,
				'report_year' 	=> 2018,
				'report_plan' 	=> 6.0,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 12,
				'report_year' 	=> 2018,
				'report_plan' 	=> 4.8,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 1,
				'report_year' 	=> 2019,
				'report_plan' 	=> 2.2,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
			[
				'report_month' 	=> 2,
				'report_year' 	=> 2019,
				'report_plan' 	=> 1.2,
				'proj_id'				=> 1,
				'created_at' 		=> date("Y-m-d H:i:s"),
			],
		]);
	}
}
