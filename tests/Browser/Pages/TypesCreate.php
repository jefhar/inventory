<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Browser\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
use Laravel\Dusk\Browser;

class TypesCreate extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url(): string
    {
        return '/types/create';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     * @return void
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }

    /**
     * @param Browser $browser
     * @param string $formName
     * @throws TimeOutException
     */
    public function createType(Browser $browser, string $formName): void
    {
        $browser
            ->waitFor('.stage-wrap');
        $wrapper = $browser->attribute('.form-wrap', 'id');
        $wrappers = explode('-', $wrapper);
        $formId = $wrappers[1];

        $browser
            ->drag('.input-control-9', '.stage-wrap') // Text field.
            ->drag('.input-control-5', '.stage-wrap') // Select field.
                ->mouseover('#frmb-' . $formId . '-fld-2')
            ->click('.icon-pencil')
            ->waitFor()
            ->type('label', 'Form Factor')
            ->click('.close-field')
            ->press('Save Form')
            ->whenAvailable(
                '.modal-dialog',
                function ($modal) use ($formName) {
                    $modal
                        ->type('saveType', $formName)
                        ->press('Save');
                }
            );
    }
}
