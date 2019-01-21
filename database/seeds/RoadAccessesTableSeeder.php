<?php

use App\Models\RoadAccess;
use Illuminate\Database\Seeder;

class RoadAccessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roads = [
            ['name' => 'Dirt road'],
            ['name' => 'Paved road'],
            ['name' => 'Easement access'],
            ['name' => 'None'],
        ];

        foreach ($roads as $road) {
            RoadAccess::query()->create($road);
        }
    }
}
