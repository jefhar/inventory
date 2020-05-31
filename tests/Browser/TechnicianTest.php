<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser;

use App\Admin\Permissions\UserRoles;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\TypesCreate;
use Tests\DuskTestCase;
use Tests\Traits\FullUsers;

class TechnicianTest extends DuskTestCase
{
    use DatabaseMigrations;
    use FullUsers;

    /**
     * @throws \Throwable
     * @test
     */
    public function testExample(): void
    {
        $this->browse(
            function (Browser $browser) {
                $browser->loginAs($this->createEmployee(UserRoles::TECHNICIAN))
                    ->visit(new TypesCreate())
                    ->createType('myProduct')
                    ->assertSee('Product Type Saved.');
            }
        );
    }
}
