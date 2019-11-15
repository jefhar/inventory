<?php

use App\User;
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
        $user = User::create([
            User::NAME => config('seeder.user_name', 'Default Admin'),
            User::EMAIL => config('seeder.user_email', 'test@example.com'),
            User::PASSWORD => Hash::make(config('seeder.user_password', 'password')),
        ]);
        $user->assignRole(\App\Admin\Permissions\UserRoles::SUPER_ADMIN);
    }
}
