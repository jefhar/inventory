<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
            '--no-sandbox',
        ]);

        switch (config('dusk.driver')) {
            case 'container':
                return RemoteWebDriver::create(
                    'http://selenium:4444/wd/hub',
                    DesiredCapabilities::chrome()->setCapability(
                        ChromeOptions::CAPABILITY,
                        $options
                    )
                );
            default: // local
                return RemoteWebDriver::create(
                    'http://localhost:9515',
                    DesiredCapabilities::chrome()->setCapability(
                        ChromeOptions::CAPABILITY,
                        $options
                    )
                );
        }
    }
}
