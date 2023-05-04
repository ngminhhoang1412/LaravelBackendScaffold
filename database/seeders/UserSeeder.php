<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
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
        DB::table((new User)->getTable())
        ->insert([
            'email' => env('ADMIN_EMAIL'),
            'name' => env('ADMIN_NAME'),
            'password' => Hash::make(env('ADMIN_PASSWORD')), 
            'remember_token' => null,
            'role' => array_keys(User::ROLES)[0]
        ]);
    }
}
