<?php

use Illuminate\Database\Seeder;
use App\Models\ListingStatus;

class ListingStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['name' => 'Incomplete'],
            ['name' => 'Available'],
            ['name' => 'Listed'],
            ['name' => 'Pending'],
            ['name' => 'Sold'],
            ['name' => 'Unavailable'],
        ];

        foreach ($statuses as $status) {
            ListingStatus::query()->create($status);
        }
    }
}
