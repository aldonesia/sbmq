<?php

use Illuminate\Database\Seeder;

class m_ProcessSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('m_process')->insert([
			[
				'proc_mne' => '0',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 0,
				'proc_name' => 'Project Created',
				'proc_shortname' => 'PC',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '1',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 1,
				'proc_name' => 'Pra Preparation',
				'proc_shortname' => 'PP',
				'proc_score' => 30,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '1.1',
				'proc_parid' => 2,
				'proc_lvl' => 2,
				'proc_seq' => 1,
				'proc_name' => 'Purchase Order',
				'proc_shortname' => '',
				'proc_score' => 10,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '1.2',
				'proc_parid' => 2,
				'proc_lvl' => 2,
				'proc_seq' => 2,
				'proc_name' => 'Material Arrival',
				'proc_shortname' => '',
				'proc_score' => 20,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '1.3',
				'proc_parid' => 2,
				'proc_lvl' => 2,
				'proc_seq' => 3,
				'proc_name' => 'Identification',
				'proc_shortname' => '',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '2',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 2,
				'proc_name' => 'Preparation',
				'proc_shortname' => 'PR',
				'proc_score' => 5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '2.1',
				'proc_parid' => 6,
				'proc_lvl' => 2,
				'proc_seq' => 1,
				'proc_name' => 'Straightening',
				'proc_shortname' => '',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '2.2',
				'proc_parid' => 6,
				'proc_lvl' => 2,
				'proc_seq' => 2,
				'proc_name' => 'Blasting',
				'proc_shortname' => '',
				'proc_score' => 2.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '2.3',
				'proc_parid' => 6,
				'proc_lvl' => 2,
				'proc_seq' => 3,
				'proc_name' => 'Primering',
				'proc_shortname' => '',
				'proc_score' => 2.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '3',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 3,
				'proc_name' => 'Fabrication',
				'proc_shortname' => 'FA',
				'proc_score' => 5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '3.1',
				'proc_parid' => 10,
				'proc_lvl' => 2,
				'proc_seq' => 1,
				'proc_name' => 'Marking',
				'proc_shortname' => '',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '3.2',
				'proc_parid' => 10,
				'proc_lvl' => 2,
				'proc_seq' => 2,
				'proc_name' => 'Cutting',
				'proc_shortname' => '',
				'proc_score' => 2.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '3.3',
				'proc_parid' => 10,
				'proc_lvl' => 2,
				'proc_seq' => 3,
				'proc_name' => 'Bending',
				'proc_shortname' => '',
				'proc_score' => 2.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '4',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 4,
				'proc_name' => 'Sub-Assembly',
				'proc_shortname' => 'SA',
				'proc_score' => 12.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '4.1',
				'proc_parid' => 14,
				'proc_lvl' => 2,
				'proc_seq' => 1,
				'proc_name' => 'Fitting',
				'proc_shortname' => '',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '4.2',
				'proc_parid' => 14,
				'proc_lvl' => 2,
				'proc_seq' => 2,
				'proc_name' => 'Welding',
				'proc_shortname' => '',
				'proc_score' => 5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '4.3',
				'proc_parid' => 14,
				'proc_lvl' => 2,
				'proc_seq' => 3,
				'proc_name' => 'Fairing',
				'proc_shortname' => '',
				'proc_score' => 7.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '5',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 5,
				'proc_name' => 'Assembly',
				'proc_shortname' => 'AS',
				'proc_score' => 12.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '5.1',
				'proc_parid' => 18,
				'proc_lvl' => 2,
				'proc_seq' => 1,
				'proc_name' => 'Cutting to Fitting',
				'proc_shortname' => '',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '5.2',
				'proc_parid' => 18,
				'proc_lvl' => 2,
				'proc_seq' => 2,
				'proc_name' => 'Fitting',
				'proc_shortname' => '',
				'proc_score' => 5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '5.3',
				'proc_parid' => 18,
				'proc_lvl' => 2,
				'proc_seq' => 3,
				'proc_name' => 'Welding',
				'proc_shortname' => '',
				'proc_score' => 7.5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '6',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 6,
				'proc_name' => 'Erection',
				'proc_shortname' => 'ER',
				'proc_score' => 35,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '6.1',
				'proc_parid' => 22,
				'proc_lvl' => 2,
				'proc_seq' => 1,
				'proc_name' => 'Cutting to Fitting',
				'proc_shortname' => '',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '6.2',
				'proc_parid' => 22,
				'proc_lvl' => 2,
				'proc_seq' => 2,
				'proc_name' => 'Fitting',
				'proc_shortname' => '',
				'proc_score' => 10,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '6.3',
				'proc_parid' => 22,
				'proc_lvl' => 2,
				'proc_seq' => 3,
				'proc_name' => 'Welding',
				'proc_shortname' => '',
				'proc_score' => 15,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '6.4',
				'proc_parid' => 22,
				'proc_lvl' => 2,
				'proc_seq' => 4,
				'proc_name' => 'Final Inspection',
				'proc_shortname' => '',
				'proc_score' => 5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '6.5',
				'proc_parid' => 22,
				'proc_lvl' => 2,
				'proc_seq' => 5,
				'proc_name' => 'Delivery',
				'proc_shortname' => '',
				'proc_score' => 5,
				'created_at' => date("Y-m-d H:i:s"),
			],
			[
				'proc_mne' => '7',
				'proc_parid' => 0,
				'proc_lvl' => 1,
				'proc_seq' => 7,
				'proc_name' => 'Project Finish',
				'proc_shortname' => 'PF',
				'proc_score' => 0,
				'created_at' => date("Y-m-d H:i:s"),
			],
		]);
	}
}
