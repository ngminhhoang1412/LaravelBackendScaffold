<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tableNames = config('permission.table_names');
        foreach (User::ROLES as $key => $value) {
            DB::table(Role::retrieveTableName())
                ->insert([
                    'name' => $key
                ]);
        }

        DB::table($tableNames['role_has_permissions'])
            ->insert([
                'permission_id' => '1',
                'role_id' => '1'
            ]);

        DB::table($tableNames['role_has_permissions'])
            ->insert([
                'permission_id' => '2',
                'role_id' => '1'
            ]);
        DB::table($tableNames['role_has_permissions'])
            ->insert([
                'permission_id' => '3',
                'role_id' => '1'
            ]);
        DB::table($tableNames['role_has_permissions'])
            ->insert([
                'permission_id' => '4',
                'role_id' => '1'
            ]);
        DB::table($tableNames['role_has_permissions'])
            ->insert([
                'permission_id' => '5',
                'role_id' => '1'
            ]);
        DB::table($tableNames['role_has_permissions'])
            ->insert([
                'permission_id' => '6',
                'role_id' => '1'
            ]);
        DB::table($tableNames['role_has_permissions'])
            ->insert([
                'permission_id' => '7',
                'role_id' => '1'
            ]);

        DB::table($tableNames['model_has_roles'])
            ->insert([
                'role_id' => 1,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ]);
    }
}
