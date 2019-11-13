<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\WorkOrderCreate;
use Tests\DuskTestCase;

class WorkOrderCreateTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function pageExists(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new WorkOrderCreate())
                ->assertSee('Work Order Estimate');
        });
    }
}
