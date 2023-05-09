<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Permission::retrieveTableName())
            ->insert([
                'name' => 'admin'
            ]);
        DB::table(Permission::retrieveTableName())
            ->insert([
                'name' => 'creator'
            ]);
        DB::table(Permission::retrieveTableName())
            ->insert([
                'name' => 'leader'
            ]);
        DB::table(Permission::retrieveTableName())
            ->insert([
                'name' => 'insight'
            ]);
        DB::table(Permission::retrieveTableName())
            ->insert([
                'name' => 'hr'
            ]);
        DB::table(Permission::retrieveTableName())
            ->insert([
                'name' => 'finance'
            ]);
        DB::table(Permission::retrieveTableName())
            ->insert([
                'name' => 'user-manage'
            ]);
    }
}
