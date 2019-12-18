<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$role_sup_admin = Role::where('name', 'Super Admin')->first();
		$role_ppc = Role::where('name', 'Production Planning Control')->first();
		$role_prj_manager  = Role::where('name', 'Project Manager')->first();
		$role_prd_manager  = Role::where('name', 'Production Manager')->first();
		$role_gen_manager  = Role::where('name', 'General Manager')->first();
		$role_qc  = Role::where('name', 'Quality Control')->first();

		$sup_admin = new User();
		$sup_admin->name = 'Super Admin';
		$sup_admin->email = 'sup_admin@email.com';
		$sup_admin->password = bcrypt('password');
		$sup_admin->email_verified_at = now();
		$sup_admin->approved_at = now();
		$sup_admin->last_login_at = now();
		$sup_admin->ip = '127.0.0.1';
		$sup_admin->save();
		$sup_admin->roles()->attach($role_sup_admin);

		$ppc = new User();
		$ppc->name = 'PPC 1';
		$ppc->email = 'ppc1@email.com';
		$ppc->password = bcrypt('password');
		$ppc->ip = '192.168.0.12';
		$ppc->last_login_at = now();
		$ppc->save();
		$ppc->roles()->attach($role_ppc);

		$prj_manager = new User();
		$prj_manager->name = 'Project Manager 1';
		$prj_manager->email = 'prj_manager1@email.com';
		$prj_manager->password = bcrypt('password');
		$prj_manager->ip = '192.168.1.23';
		$prj_manager->last_login_at = now();
		$prj_manager->save();
		$prj_manager->roles()->attach($role_prj_manager);

		$ppc = new User();
		$ppc->name = 'PPC 2';
		$ppc->email = 'ppc2@email.com';
		$ppc->password = bcrypt('password');
		$ppc->ip = '10.151.23.45';
		$ppc->last_login_at = now();
		$ppc->save();
		$ppc->roles()->attach($role_ppc);

		$prd_manager = new User();
		$prd_manager->name = 'Production Manager 1';
		$prd_manager->email = 'prd_manager1@email.com';
		$prd_manager->password = bcrypt('password');
		$prd_manager->ip = '10.151.10.151';
		$prd_manager->last_login_at = now();
		$prd_manager->save();
		$prd_manager->roles()->attach($role_prd_manager);

		$gen_manager = new User();
		$gen_manager->name = 'General Manager 1';
		$gen_manager->email = 'gen_manager1@email.com';
		$gen_manager->password = bcrypt('password');
		$gen_manager->ip = '10.151.151.10';
		$gen_manager->last_login_at = now();
		$gen_manager->save();
		$gen_manager->roles()->attach($role_gen_manager);

		$qc = new User();
		$qc->name = 'QC 1';
		$qc->email = 'qc1@email.com';
		$qc->password = bcrypt('password');
		$qc->ip = '36.74.97.216';
		$qc->last_login_at = now();
		$qc->save();
		$qc->roles()->attach($role_qc);
	}
}
