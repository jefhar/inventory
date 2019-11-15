<?php

namespace Tests\Browser;

use App\Admin\Controllers\WorkOrdersController;
use App\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\WorkOrderCreate;
use Tests\DuskTestCase;

class WorkOrderCreateTest extends DuskTestCase
{

    /**
     * @test
     */
    public function pageExists(): void
    {
        $user = factory(User::class)->create();
        $user->givePermissionTo(WorkOrdersController::CREATE_NAME);

        $this->browse(
            function (Browser $browser) use ($user) {
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
