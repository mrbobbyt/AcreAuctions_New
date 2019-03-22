<?php

use Illuminate\Database\Seeder;
use App\Models\Utility;

class UtilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $utilities = [
            ['name' => 'Power'],
            ['name' => 'Sewer'],
            ['name' => 'Septic'],
            ['name' => 'City/County Water'],
            ['name' => 'Well'],
            ['name' => 'Gas'],
            ['name' => 'Telephone'],
        ];

        foreach ($utilities as $u) {
            Utility::query()->create($u);
        }
    }
}
