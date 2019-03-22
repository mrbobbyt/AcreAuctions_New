<?php

use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Seller::query()->create([
            'user_id' => 1,
            'title' => 'Dev seller',
            'slug' => 'dev_seller',
            'description' => 'Some description about seller',
            'is_verified' => true,
            'address' => '1234 Dev Seller str',
        ]);
    }
}
