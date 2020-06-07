<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Traits\FullObjects;

class LoginLogoutTest extends DuskTestCase
{
    use DatabaseMigrations;
    use FullObjects;

    /**
     * @throws \Throwable
     * @test
     */
    public function loginLogout(): void
    {
        $this->browse(
            function (Browser $browser) {
                $employee = $this->createEmployee();
                $browser->visit('/')
                    ->clickLink('Login')
                    ->type('email', $employee->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->clickLink('Logout')
                    ->assertPathIs('/')
                    ->assertGuest();
            }
        );
    }
}
