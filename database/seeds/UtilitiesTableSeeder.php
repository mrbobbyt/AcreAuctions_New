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
            ['name' => 'No Utilities'],
            ['name' => 'Power available, needs well, and septic'],
            ['name' => 'Power available, water available, needs septic'],
            ['name' => 'Power, Water, Sewer Available'],
            ['name' => 'Full Utilities Available'],
            ['name' => 'Contact County for Utilities'],
        ];

        foreach ($utilities as $u) {
            Utility::query()->create($u);
        }
    }
}
