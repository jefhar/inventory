<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser;

use App\Admin\Permissions\UserRoles;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Traits\FullUsers;

/**
 * Class CanLogInTest
 *
 * @package Tests\Browser
 * @codeCoverageIgnore
 */
class CanLogInTest extends DuskTestCase
{
    use DatabaseMigrations;
    use FullUsers;

    /**
     * @test
     * @throws \Throwable
     */
    public function technicianCanLogin(): void
    {
        $technician = $this->createEmployee(UserRoles::TECHNICIAN);

        $this->browse(
            static function (Browser $browser) use ($technician) {
                $browser->visit('/login')
                    ->type('email', $technician->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->assertSee($technician->name)
                    ->clickLink($technician->name)
                    ->assertSee('Logout');
                //              $browser->loginAs($user)
                //                   ->visit(new WorkOrderCreate())->dd();
                //                    ->assertSee('Work Order Estimate');
            }
        );
    }
}
