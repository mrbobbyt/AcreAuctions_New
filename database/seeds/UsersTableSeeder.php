<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 10)->create();
        User::query()->create([
            'fname' => 'John',
            'lname' => 'Doe',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'email_verified_at' => date('Y-m-d h:i:s'),
            'role' => 1,
        ]);

        User::query()->create([
            'fname' => 'John',
            'lname' => 'Content Manager',
            'email' => 'content.manager@example.com',
            'password' => bcrypt('secret'),
            'email_verified_at' => date('Y-m-d h:i:s'),
            'role' => 4,
        ]);
    }
}
