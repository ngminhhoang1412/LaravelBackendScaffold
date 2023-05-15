<?php

namespace Database\Seeders;

use App\Models\AbsenceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = AbsenceType::ABSENCE_TYPES;
        foreach ($types as $key => $value) {
            DB::table(AbsenceType::retrieveTableName())
                ->insert([
                    'code' => $key,
                    'description' => $value
                ]);
        }
    }
}
