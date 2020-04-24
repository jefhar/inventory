<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser;

use App\Admin\Permissions\UserRoles;
use App\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\WorkOrderCreate;
use Tests\DuskTestCase;

/**
 * Class WorkOrderCreateTest
 *
 * @package Tests\Browser
 * @codeCoverageIgnore
 */
class WorkOrderCreateTest extends DuskTestCase
{

    /**
     * @test
     * @throws \Throwable
     */
    public function pageExists(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::SALES_REP);
        $user->save();

        $this->browse(
            static function (Browser $browser) use ($user) {
                $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->visit(new WorkOrderCreate())
                    ->assertSee('Create Work Order');
                //              $browser->loginAs($user)
                //                   ->visit(new WorkOrderCreate())->dd();
                //                    ->assertSee('Work Order Estimate');
            }
        );
    }
}
