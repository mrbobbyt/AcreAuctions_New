<?php

use App\Models\SaleType;
use Illuminate\Database\Seeder;

class SaleTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Cash'],
            ['name' => 'Owner financing'],
        ];

        foreach ($types as $type) {
            SaleType::query()->create($type);
        }
    }
}
