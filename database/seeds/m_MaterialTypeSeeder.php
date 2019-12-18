<?php

use Illuminate\Database\Seeder;

class m_MaterialTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// declare data
		$material_name = ['Plate', 'Profile', 'Others Structure'];

		$material[0] =   [
			'Keel Plate', 'Bottom Plate', 'Plate Between Chine', 'Trnasom Plate', 'Side Sheel Plate', 'Sheer Strake',
			'Bulwark Side Plate', 'Stern Plate', 'Bulwark Aft', 'Deck Plate', 'Plate Floor', 'Transverse Bulkhead State',
			'Long Bulkhead Plate', 'Engine Girder Plate', 'Engine Seating Plate', 'Transverse Wall Plate', 'Collar Plate',
			'Outboard Engine Girder', 'Outboard Engine Seating', 'Tank Top Plate', 'Inboard Engine Girder Plate',
			'Outboard Engine Girder Plate'
		];
		$material[1] =   [
			'Center Girder', 'Side Girder', 'Stem Stringer', 'Aft Bullwark', 'Web Frame', 'Ordinary Frame', 'Bullwark Stay',
			'Transverse Bulkhead', 'Deck Girder', 'Side Deck Transverse', 'Center Deck Transverse', 'Deck Transverse', 'Side Stringer',
			'Deck Beam', 'Stiffener Plate Floor', 'Center Pillar', 'Side Pillar', 'Pillar', 'Pillar', 'Girder Under Pillar',
			'Steam Bar', 'Inner Bottom Transverse'
		];
		$material[2] =   [
			'Upper Chine', 'Lower Chine', 'Upper Aft Bracket', 'Upper Fore Bracket', 'Lower Aft Bracket', 'Lower Fore Bracket',
			'Bracket', 'Bracket Between Siffener', 'DBLR', 'Upper Fore Bracket of Pillar', 'Upper Bracket Siffener Long Bulkhead',
			'Lower Bracket Siffener Long Bulkhead'
		];

		// insert
		$flag_id = 0;
		for ($i = 0; $i < sizeof($material_name); $i++) {
			$var = $i + 1;
			DB::table('m_material_type')->insert([
				'mt_mne' => sprintf("%d", $var),
				'mt_parid' => 0,
				'mt_lvl' => 1,
				'mt_seq' => $var,
				'mt_name' => $material_name[$i],
				'created_at' => date("Y-m-d H:i:s"),
			]);

			$parid = $flag_id + $var;
			for ($j = 1; $j < sizeof($material[$i]) + 1; $j++) {
				DB::table('m_material_type')->insert([
					'mt_mne' => sprintf("%d.%d", $var, $j),
					'mt_parid' => $parid,
					'mt_lvl' => 2,
					'mt_seq' => $j,
					'mt_name' => sprintf("%s", $material[$i][$j - 1]),
					'created_at' => date("Y-m-d H:i:s"),
				]);
				$flag_id++;
			}
		};
	}
}
