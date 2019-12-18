<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(RoleTableSeeder::class);
		$this->call(UserTableSeeder::class);
		$this->call(m_ProcessSeeder::class);
		$this->call(m_MaterialTypeSeeder::class);
		$this->call(m_PanelPositionSeeder::class);
		$this->call(m_PanelTypeSeeder::class);
		$this->call(m_BlockTypeSeeder::class);
		$this->call(m_StatusSeeder::class);
		$this->call(m_ProjectSeeder::class);
		$this->call(tb_MaterialSeeder::class);
		$this->call(tb_PiecepartSeeder::class);
		$this->call(tb_PanelSeeder::class);
		$this->call(tb_BlockSeeder::class);
		$this->call(tb_ShipSeeder::class);
		$this->call(tb_ReportSeeder::class);
	}
}
