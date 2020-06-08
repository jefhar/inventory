<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\WorkOrders\Controllers\WorkOrderController;
use Laravel\Dusk\Browser;

/**
 * Class WorkOrderCreate
 *
 * @package Tests\Browser\Pages
 * @codeCoverageIgnore
 */
class WorkOrderCreate extends Page
{
    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return WorkOrderController::CREATE_PATH;
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
