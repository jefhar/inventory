<?php

use App\Admin\Permissions\UserRoles;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var User $technician */
        /** @var User $employee */
        /** @var User $superAdmin */
        /** @var User $owner */
        /** @var User $salesRep */

        $environment = App::environment();
        if (strtolower($environment) === 'production') {
            exit('Cannot seed users in a production environment.');
        }

        $superAdmin = User::create(
            [
                User::NAME => config('seeder.super_admin.name', 'Default SuperAdmin'),
                User::EMAIL => config('seeder.super_admin.email', 'superadmin@example.com'),
                User::PASSWORD => Hash::make(config('seeder.super_admin.password', 'password')),
            ]
        );
        $superAdmin->assignRole(UserRoles::SUPER_ADMIN);

        $owner = User::create(
            [
                User::NAME => config('seeder.owner.name', 'Default Owner'),
                User::EMAIL => config('seeder.owner.email', 'owner@example.com'),
                User::PASSWORD => Hash::make(config('seeder.owner.password', 'password')),
            ]
        );
        $owner->assignRole(UserRoles::OWNER);

        $salesRep = User::create(
            [
                User::NAME => config('seeder.sales_rep.name', 'Default Sales Rep'),
                User::EMAIL => config('seeder.sales_rep.email', 'salesrep@example.com'),
                User::PASSWORD => Hash::make(config('seeder.sales_rep.password', 'password')),
            ]
        );
        $salesRep->assignRole(UserRoles::SALES_REP);

        $technician = User::create(
            [
                User::NAME => config('seeder.technician.name', 'Default Technician'),
                User::EMAIL => config('seeder.technician.email', 'technician@example.com'),
                User::PASSWORD => Hash::make(config('seeder.technician.password', 'password')),
            ]
        );
        $technician->assignRole(UserRoles::TECHNICIAN);

        $employee = User::create(
            [
                User::NAME => config('seeder.employee.name', 'Default Employee'),
                User::EMAIL => config('seeder.employee.email', 'employee@example.com'),
                User::PASSWORD => Hash::make(config('seeder.employee.password', 'password')),
            ]
        );
        $employee->assignRole(UserRoles::EMPLOYEE);
    }
}
