<?php

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Cheap Land for Sale'],
            ['name' => 'Residential Lots'],
            ['name' => 'Recreational Land'],
            ['name' => 'Ranches/Larger Acreage'],
            ['name' => 'Cheap houses'],
        ];

        foreach ($types as $type) {
            PropertyType::query()->create($type);
        }
    }
}
