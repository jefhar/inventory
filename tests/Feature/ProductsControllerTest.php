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
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\WorkOrder;
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
        $type = factory(Type::class)->create();
        $workOrder = factory(WorkOrder::class)->create();
        $formRequest = [
            'type' => $type->slug,
            'workorderId' => $workOrder->id,
            'radio-group-1575689472139' => 'option-3',
            'select-1575689474390' => 'option-2',
            'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
        ];

        $this->actingAs($this->user)
            ->withoutExceptionHandling()
            ->postJson(route(ProductsController::STORE_NAME), $formRequest)
            ->assertCreated()
            ->assertSee(Product::ID)
            ->assertSee(Product::TYPE_ID)
            ->assertSee(Product::WORK_ORDER_ID);

        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::TYPE_ID => $type->id,
                Product::WORK_ORDER_ID => $workOrder->id,
                Product::ID => 1,
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
