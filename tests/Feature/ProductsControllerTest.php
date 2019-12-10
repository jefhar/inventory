<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserPermissions;
use App\Products\Controllers\ProductsController;
use App\User;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\WorkOrder;
use Faker\Factory;
use Tests\TestCase;

/**
 * Class ProductsControllerTest
 *
 * @package Tests\Feature
 */
class ProductsControllerTest extends TestCase
{
    private User $user;

    /**
     * @test
     */
    public function storeProductReturnsProduct(): void
    {
        $faker = Factory::create();
        $manufacturer = $faker->company;
        $model = $faker->title;
        $type = factory(Type::class)->create();
        $workOrder = factory(WorkOrder::class)->create();
        $formRequest = [
            'manufacturer' => $manufacturer,
            'model' => $model,
            'radio-group-1575689472139' => 'option-3',
            'select-1575689474390' => 'option-2',
            'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
            'type' => $type->slug,
            'workorderId' => $workOrder->id,
        ];

        $this->actingAs($this->user)
            ->withoutExceptionHandling()
            ->postJson(route(ProductsController::STORE_NAME), $formRequest)
            ->assertCreated()
            ->assertSee($manufacturer)
            ->assertSee(Product::ID)
            ->assertSee(Product::TYPE_ID)
            ->assertSee(Product::WORK_ORDER_ID);

        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => 1,
                Product::MODEL => $model,
                Product::TYPE_ID => $type->id,
                Product::WORK_ORDER_ID => $workOrder->id,
            ]
        );
        $this->assertDatabaseHas(
            Manufacturer::TABLE,
            [
                Manufacturer::ID => 1,
                Manufacturer::NAME => $manufacturer,
            ]
        );
    }

    public function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = factory(User::class)->make()
            ->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $user->save();
        $this->user = $user;
    }
}
