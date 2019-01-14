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
            ['name' => 'Agrocultural'],
            ['name' => 'Commercial'],
            ['name' => 'Mixed Use'],
            ['name' => 'Recreational'],
            ['name' => 'Other (See Zoning Description)'],
        ];

        foreach ($zoning as $zone) {
            Zoning::query()->create($zone);
        }
    }
}
