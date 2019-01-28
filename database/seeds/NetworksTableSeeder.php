<?php

use App\Models\Network;
use Illuminate\Database\Seeder;

class NetworksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $networks = [
            ['name' => 'facebook'],
            ['name' => 'twitter'],
            ['name' => 'gplus'],
            ['name' => 'reddit'],
        ];

        foreach ($networks as $network) {
            Network::query()->create($network);
        }
    }
}
