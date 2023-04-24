<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table((new User)->getTable())
            ->insert([
                'email' => env('ADMIN_EMAIL'),
                'name' => env('ADMIN_USERNAME'),
                'password' => env('ADMIN_PASSWORD'), //password
                'remember_token' => null,
                'role' => array_keys(User::ROLES)[0]
            ]);
    }
}
