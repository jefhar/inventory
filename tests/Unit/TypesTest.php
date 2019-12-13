<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Products\Models\Type;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class TypesTest
 *
 * @package Tests\Unit
 */
class TypesTest extends TestCase
{
    /**
     * @test
     */
    public function typeCreatesItsOwnSlug(): void
    {
        $type = factory(Type::class)->create();
        $type->fresh();
        $this->assertEquals(Str::slug($type->name), $type->slug);
        $this->assertEquals(Str::title($type->name), $type->name);
    }
}
