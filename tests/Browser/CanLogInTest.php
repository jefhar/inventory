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
use Tests\Browser\Pages\WorkOrderCreate;
use Tests\DuskTestCase;

/**
 * Class CanLogInTest
 *
 * @package Tests\Browser
 * @codeCoverageIgnore
 */
class CanLogInTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @throws \Throwable
     */
    public function technicianCanLogin(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::TECHNICIAN);
        $user->save();

        $this->browse(
            static function (Browser $browser) use ($user) {
                $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->assertSee($user->name);
                //              $browser->loginAs($user)
                //                   ->visit(new WorkOrderCreate())->dd();
                //                    ->assertSee('Work Order Estimate');
            }
        );
    }
}
