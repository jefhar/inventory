<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\Products;

use App\Admin\Permissions\UserRoles;
use App\Products\DataTransferObject\RawProductUpdateObject;
use App\User;
use Domain\Products\Actions\ProductShowAction;
use Domain\Products\Actions\RawProductUpdateAction;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
use Tests\TestCase;

/**
 * Class ProductsTest
 *
 * @package Tests\Unit
 */
class ProductsTest extends TestCase
{

    /**
     * @test
     */
    public function productHasManufacturerAndModel(): void
    {
        $faker = Factory::create();
        $manufacturerName = $faker->company;
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $manufacturer = Manufacturer::firstOrCreate([Manufacturer::NAME => $manufacturerName]);
        $product->manufacturer()->associate($manufacturer);
        $product->workOrder()->associate($workOrder);
        $product->save();
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::MANUFACTURER_ID => $manufacturer->id,
            ]
        );
        $this->assertDatabaseHas(
            Manufacturer::TABLE,
            [
                Manufacturer::ID => $manufacturer->id,
                Manufacturer::NAME => $manufacturer->name,
            ]
        );
        $product->fresh();
        $this->assertEquals($manufacturerName, $product->manufacturer->name);
    }

    /**
     * @test
     */
    public function createdProductHasLuhn(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => 1,
                Product::LUHN => 18,
            ]
        );
    }

    /**
     * @test
     */
    public function createdProductIsAvailableForSale(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => 1,
                Product::STATUS => Product::STATUS_AVAILABLE,
            ]
        );
    }

    /**
     * @test
     */
    public function updateProductUpdatesProduct(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $update = factory(Product::class)->make();
        $productUpdateObject = RawProductUpdateObject::fromRequest(
            [
                'type' => $update->type->slug,
                'manufacturer' => $update->manufacturer->name,
                'model' => $update->model,
                'values' => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
                ],
            ]
        );
        RawProductUpdateAction::execute($product, $productUpdateObject);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::MODEL => $productUpdateObject->model,
                Product::VALUES => json_encode(
                    [
                        'radio-group-1575689472139' => 'option-3',
                        'select-1575689474390' => 'option-2',
                        'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
                    ],
                    JSON_THROW_ON_ERROR,
                    512
                ),
            ]
        );
    }

    /**
     * @test
     */
    public function updateProductCreatesSerialField(): void
    {
        $faker = Factory::create();
        $serial = $faker->isbn13;
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make(
            [
                'values' => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'text-1575689474910' => 'option-1',
                ],
            ]
        );

        $workOrder->products()->save($product);
        $this->assertDatabaseMissing(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::SERIAL => $serial,
            ]
        );

        $productUpdateObject = RawProductUpdateObject::fromRequest(
            [
                'type' => $product->type->slug,
                'manufacturer' => $product->manufacturer->name,
                'model' => $product->model,
                'values' => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'text-1575689474910' => 'option-1',
                    'serial' => $serial,
                ],
            ]
        );
        RawProductUpdateAction::execute($product, $productUpdateObject);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::SERIAL => $serial,
            ]
        );
    }

    /**
     * @test
     */
    public function renderingProductViewCombinesValuesAndType(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::EMPLOYEE);
        $this->actingAs($user);

        $formData = ProductShowAction::execute($product);
        $this->assertArrayHasKey('userData', $formData[2]);
    }

    /**
     * @test
     */
    public function renderingProductViewAddsManufacturerAndModelFirst(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::EMPLOYEE);
        $this->actingAs($user);

        $formData = json_decode($product->type->form, true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayNotHasKey('manufacturer', $formData);
        $actionFormData = ProductShowAction::execute($product);
        $this->assertEquals('manufacturer', $actionFormData[0]['name']);
        $this->assertEquals('model', $actionFormData[1]['name']);
    }
}
