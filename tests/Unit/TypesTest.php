<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Products\DataTransferObject\TypeStoreObject;
use Domain\Products\Actions\TypeStoreAction;
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

    /**
     * @test
     */
    public function canSaveNewType(): void
    {
        $type = factory(Type::class)->make();

        $typeStoreObject = TypeStoreObject::fromRequest(
            [
                Type::NAME => $type->name,
                Type::FORM => $type->form,
            ]
        );
        TypeStoreAction::execute($typeStoreObject);
        $this->assertDatabaseHas(
            Type::TABLE,
            [
                Type::NAME => Str::title($type->name),
                Type::SLUG => Str::slug($type->name),
                Type::FORM => $type->form,
            ]
        );
    }

    /**
     * @test
     */
    public function canUpdateType(): void
    {
        $type = factory(Type::class)->create();
        $form = json_decode($type->form, true, 512, JSON_THROW_ON_ERROR);
        array_pop($form);
        array_pop($form);
        $form = json_encode($form, JSON_THROW_ON_ERROR, 512);
        $typeStoreObject = TypeStoreObject::fromRequest(
            [Type::NAME => $type->name, Type::FORM => $form]
        );
        $updatedType = TypeStoreAction::execute($typeStoreObject);
        $this->assertDatabaseHas(
            Type::TABLE,
            [
                Type::NAME => Str::title($updatedType->name),
                Type::SLUG => Str::slug($updatedType->name),
                Type::FORM => $updatedType->form,
            ]
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function canSoftDeleteType(): void
    {
        $type = factory(Type::class)->make();

        $typeStoreObject = TypeStoreObject::fromRequest(
            [
                Type::NAME => $type->name,
                Type::FORM => $type->form,
            ]
        );
        TypeStoreAction::execute($typeStoreObject);
        $this->assertDatabaseHas(Type::TABLE, [Type::NAME => Str::title($type->name)]);
        $freshType = Type::where(Type::NAME, $type->name)->first();
        $freshType->delete();
        $this->assertSoftDeleted($freshType);
    }
}
