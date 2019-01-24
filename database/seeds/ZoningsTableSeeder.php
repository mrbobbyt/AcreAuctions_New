<?php

use Illuminate\Database\Seeder;
use App\Models\Zoning;

class ZoningsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zoning = [
            ['name' => 'Residential'],
            ['name' => 'Rural Residential'],
            ['name' => 'Agricultural'],
            ['name' => 'Commercial'],
            ['name' => 'Mixed Use'],
        ];

        foreach ($zoning as $zone) {
            Zoning::query()->create($zone);
        }
    }
}
