<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page as BasePage;

/**
 * Class Page
 *
 * @package Tests\Browser\Pages
 * @codeCoverageIgnore
 */
abstract class Page extends BasePage
{
    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
