<?php

namespace Tests\Unit;

use Domain\Products\Models\Type;
use Faker\Factory;
use Illuminate\Support\Str;
use Tests\TestCase;

class TypesTest extends TestCase
{
    /**
     * @test
     */
    public function typeGivenANameHasASlug(): void
    {
        $faker = Factory::create();
        $name = $faker->words(2, true);
        $slug = Str::slug($name);
        $type = factory(Type::class)->make();
        $type->name = $name;
        $type->save();
        $type->fresh();
        $this->assertEquals($slug, $type->slug);
    }
}
