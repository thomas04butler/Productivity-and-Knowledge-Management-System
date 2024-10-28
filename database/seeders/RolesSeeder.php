<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create the roles for the different level of users
        $manager = Role::create(['name' => 'manager']);
        $leader = Role::create(['name' => 'leader']);
        $member = Role::create(['name' => 'member']);

        // create the permissions for the roles
        $view_all = Permission::create(['name' => 'view all']);
        $view_team = Permission::create(['name' => 'view team']);
        $view_personal = Permission::create(['name' => 'view personal']);

        // assign the permissions to the roles
        $manager->givePermissionTo($view_all);
        $manager->givePermissionTo($view_personal);
        $leader->givePermissionTo($view_team);
        $leader->givePermissionTo($view_personal);
        $member->givePermissionTo($view_personal);
    }
}
