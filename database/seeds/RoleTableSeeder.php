<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$role_sup_admin = new Role();
		$role_sup_admin->name = 'Super Admin';
		$role_sup_admin->description = 'User Activation, View & Edit';
		$role_sup_admin->created_at = date("Y-m-d H:i:s");
		$role_sup_admin->save();

		$role_ppc = new Role();
		$role_ppc->name = 'Production Planning Control';
		$role_ppc->description = 'View & Edit';
		$role_ppc->created_at = date("Y-m-d H:i:s");
		$role_ppc->save();

		$role_prj_manager = new Role();
		$role_prj_manager->name = 'Project Manager';
		$role_prj_manager->description = 'View';
		$role_prj_manager->created_at = date("Y-m-d H:i:s");
		$role_prj_manager->save();

		$role_prd_manager = new Role();
		$role_prd_manager->name = 'Production Manager';
		$role_prd_manager->description = 'View';
		$role_prd_manager->created_at = date("Y-m-d H:i:s");
		$role_prd_manager->save();

		$role_gen_manager = new Role();
		$role_gen_manager->name = 'General Manager';
		$role_gen_manager->description = 'View';
		$role_gen_manager->created_at = date("Y-m-d H:i:s");
		$role_gen_manager->save();

		$role_qc = new Role();
		$role_qc->name = 'Quality Control';
		$role_qc->description = 'View';
		$role_qc->created_at = date("Y-m-d H:i:s");
		$role_qc->save();
	}
}
