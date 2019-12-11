<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Products\Models\Type;
use Faker\Factory;
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
    public function typeGivenANameHasASlug(): void
    {
        $faker = Factory::create();
        $name = Str::lower($faker->words(2, true));
        $slug = Str::slug($name);
        $type = factory(Type::class)->make();
        $type->name = $name;
        $type->save();
        $type->fresh();
        $this->assertEquals($slug, $type->slug);
        $this->assertEquals($type->name, Str::title($name));
    }
}
