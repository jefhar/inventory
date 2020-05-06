<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser;

use App\Admin\Permissions\UserRoles;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Traits\FullUsers;

class SalesRepTest extends DuskTestCase
{
    use DatabaseMigrations;
    use FullUsers;

    /**
     * @throws \Throwable
     * @test
     */
    public function salesRepCanLogin(): void
    {
        $faker = Factory::create();
        $user = $this->createEmployee(UserRoles::SALES_REP);
        $this->browse(
            function (Browser $browser) use ($faker, $user) {
                $companyName = $faker->company;
                $firstName = $faker->firstName;
                $lastName = $faker->lastName;

                $browser->visit('/')
                    ->clickLink('Login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->waitForText('Create new WorkOrder')
                    ->click('Create new WorkOrder')
                    ->type('company_name', $companyName)
                    ->type('first_name', $firstName)
                    ->type('last_name', $lastName)
                    ->press('Create New Work Order')
                    ->waitFor('#workorders_edit')
                    ->assertSee($companyName)
                    ->assertSee($firstName . ' ' . $lastName);
            }
        );
    }
}
