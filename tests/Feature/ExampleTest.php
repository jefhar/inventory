<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Class ExampleTest
 *
 * @package Tests\Feature
 */
class ExampleTest extends TestCase
{
    /**
     * You get one for free.
     *
     * @return void
     */
    public function testBasicTest(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
