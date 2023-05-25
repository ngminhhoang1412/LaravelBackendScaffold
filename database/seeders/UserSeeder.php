<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\AbsenceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tableNames = config('permission.table_names');
        $admin = DB::table((new User)->getTable())
            ->insertGetId([
                'email' => env('ADMIN_EMAIL'),
                'name' => env('ADMIN_NAME'),
                'password' => Hash::make(env('ADMIN_PASSWORD')),
                'remember_token' => null,
                'role' => array_keys(User::ROLES)[0]
            ]);

        $adminRoleId = DB::table(Role::retrieveTableName())->where('name', '=', 'admin')->get('id');
        DB::table($tableNames['model_has_roles'])
            ->insert([
                'role_id' => $adminRoleId[0]->id,
                'model_type' => User::class,
                'model_id' => $admin
            ]);

        $absenceTypes = AbsenceType::ABSENCE_TYPES;
        $excludedCodes = ['W', 'W/2'];
        DB::table(AbsenceType::retrieveTableName())
            ->whereNotIn('code', $excludedCodes)
            ->get()
            ->each(function ($value) use ($admin, $absenceTypes) {
                $absenceType = $absenceTypes[$value->code];
                DB::table(AbsenceType::INTERMEDIATE_TABLES[0])
                    ->insert([
                        'user_id' => $admin,
                        'absence_type_id' => $value->id,
                        'amount' => $absenceType['default_amount']
                    ]);
            });
    }
}
