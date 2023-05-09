<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $platform = file(storage_path() . "/resource/platforms.txt", FILE_IGNORE_NEW_LINES);
        $platform = collect($platform)->map(function ($item) {
            $separate = explode(":", $item);
            return [
                'name' => $separate[0],
                'description' => $separate[1],
                'logo' => $separate[2]
            ];
        })->toArray();
        DB::table(Platform::retrieveTableName())
            ->insert($platform);
    }
}
