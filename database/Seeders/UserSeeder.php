<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $adminRole = Role::where('name', 'admin')->value('id');
        $userRole = Role::where('name', 'user')->value('id');
        $users = [
            [
                'name' => 'Admin',
                'password' => Hash::make('admin@mail.com'),
                'email' => 'admin@mail.com',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'User',
                'password' => Hash::make('user@mail.com'),
                'email' => 'user@mail.com',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'User 1',
                'password' => Hash::make('user1@mail.com'),
                'email' => 'user1@mail.com',
            ],
        ];

        foreach ($users as $user) {
            $model = User::updateOrCreate(
                [
                    'email' => $user['email'],
                ],
                $user
            );

            if ($user['email'] == 'admin@mail.com') {
                $model->roles()->sync([$adminRole]);
            } else {
                $model->roles()->sync([$userRole]);
            }
        }
    }
}
