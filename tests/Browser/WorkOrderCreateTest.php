<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser;

use App\User;
use App\WorkOrders\Controllers\WorkOrdersController;
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
        $user = factory(User::class)->create();
        $user->givePermissionTo(WorkOrdersController::CREATE_NAME);

        $this->browse(
            static function (Browser $browser) use ($user) {
                $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->visit(new WorkOrderCreate())
                    ->assertSee('Work Order Estimate');
                //              $browser->loginAs($user)
                //                   ->visit(new WorkOrderCreate())->dd();
                //                    ->assertSee('Work Order Estimate');
            }
        );
    }
}
