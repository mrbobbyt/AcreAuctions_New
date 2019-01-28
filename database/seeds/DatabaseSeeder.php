<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);
         $this->call(RolesTableSeeder::class);
         $this->call(UtilitiesTableSeeder::class);
         $this->call(ZoningsTableSeeder::class);
         $this->call(RoadAccessesTableSeeder::class);
         $this->call(PropertyTypesTableSeeder::class);
         $this->call(SaleTypesTableSeeder::class);
         $this->call(NetworksTableSeeder::class);
    }
}
