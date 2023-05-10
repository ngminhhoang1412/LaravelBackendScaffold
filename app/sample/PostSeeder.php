<?php


namespace App\sample;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Post::retrieveTableName())
            ->insert([
                'user_id' => 1,
                'description' => "This is a test post"
            ]);
    }
}
