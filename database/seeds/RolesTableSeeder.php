<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'admin'],
            ['name' => 'seller'],
            ['name' => 'buyer'],
        ];

        foreach ($roles as $role) {
            Role::query()->create($role);
        }
    }
}
