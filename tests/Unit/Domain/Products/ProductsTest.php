<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\Products;

use App\Admin\Exceptions\LockedProductException;
use App\Admin\Permissions\UserRoles;
use App\Carts\DataTransferObjects\CartPatchObject;
use App\Products\DataTransferObject\InventoryProductUpdateObject;
use Domain\Carts\Actions\CartPatchAction;
use Domain\Carts\Models\Cart;
use Domain\PendingSales\Actions\PricePatchAction;
use Domain\Products\Actions\InventoryProductUpdateAction;
use Domain\Products\Actions\ProductShowAction;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class ProductsTest
 *
 * @package Tests\Unit
 */
class ProductsTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function productHasManufacturerAndModel(): void
    {
        $faker = Factory::create();
        $manufacturerName = $faker->company;
        $workOrder = factory(WorkOrder::class)->create();
        /** @var Product $product */
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
        $this->createFullProduct();
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
        $this->createFullProduct();
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
     * @throws \JsonException
     */
    public function updateProductUpdatesProduct(): void
    {
        $product = $this->createFullProduct();
        $update = factory(Product::class)->make();
        $productUpdateObject = InventoryProductUpdateObject::fromRequest(
            [
                Product::TYPE => $update->type->slug,
                Product::MANUFACTURER_NAME => $update->manufacturer->name,
                Product::MODEL => $update->model,
                Product::VALUES => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
                ],
            ]
        );
        InventoryProductUpdateAction::execute($product, $productUpdateObject);
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
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        /** @var Product $product */
        $product = factory(Product::class)->make(
            [
                Product::VALUES => [
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

        $productUpdateObject = InventoryProductUpdateObject::fromRequest(
            [
                Product::TYPE => $product->type->slug,
                Product::MANUFACTURER_NAME => $product->manufacturer->name,
                Product::MODEL => $product->model,
                Product::VALUES => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'text-1575689474910' => 'option-1',
                    'serial' => $serial,
                ],
            ]
        );
        InventoryProductUpdateAction::execute($product, $productUpdateObject);
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
     * @throws \JsonException
     */
    public function renderingProductViewCombinesValuesAndType(): void
    {
        $product = $this->createFullProduct();
        $this->actingAs($this->createEmployee(UserRoles::EMPLOYEE));

        $formData = ProductShowAction::execute($product);
        $this->assertArrayHasKey('userData', $formData[2]);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function renderingProductViewAddsManufacturerAndModelFirst(): void
    {
        $product = $this->createFullProduct();

        $this->actingAs($this->createEmployee(UserRoles::EMPLOYEE));

        $formData = json_decode($product->type->form, true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayNotHasKey(Product::MANUFACTURER_NAME, $formData);
        $actionFormData = ProductShowAction::execute($product);
        $this->assertEquals(Product::MANUFACTURER_NAME, $actionFormData[0]['name']);
        $this->assertEquals(Product::MODEL, $actionFormData[1]['name']);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function productPriceSavesAsPennies(): void
    {
        $product = $this->createFullProduct();
        $price = rand(100, 999_999_99);
        $product->price = $price / 100;
        $product->save();
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::PRICE => (int)($price),
            ]
        );
        $product->refresh();
        $this->assertEquals($price, $product->price * 100);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function productPriceCanNotBeNegative(): void
    {
        $product = $this->createFullProduct();
        $price = rand(-999_999_99, 0);
        $product->price = $price;
        $product->save();
        $product->refresh();
        $this->assertEquals(0, $product->price);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function invoicedProductCannotChangePrice(): void
    {
        Mail::fake();
        // Setup
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);

        $product = $this->createFullProduct();
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $cart->products()->save($product);
        CartPatchAction::execute($cart, CartPatchObject::fromRequest([Cart::STATUS => Cart::STATUS_INVOICED]));
        $product->refresh();

        // Test
        $this->expectException(LockedProductException::class);
        PricePatchAction::execute($product, rand(0, 999_999_99) / 100);
    }
}
