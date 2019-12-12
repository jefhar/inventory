<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\Types\Controllers\TypesController;
use App\User;
use Domain\Products\Models\Type;
use Tests\TestCase;

/**
 * Class TypesControllerTest
 *
 * @package Tests\Feature
 */
class TypesControllerTest extends TestCase
{
    private User $user;
    private User $guest;

    /**
     * @test
     */
    public function unauthorizedTypesControllerIsUnauthorized(): void
    {
        $type = factory(Type::class)->create();
        $this
            ->actingAs($this->guest)
            ->get(route(TypesController::SHOW_NAME, $type->slug))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function authorizedTypesControllerIsOk(): void
    {
        $type = factory(Type::class)->create();
        $this
            ->withoutExceptionHandling()
            ->actingAs($this->user)
            ->get(route(TypesController::SHOW_NAME, $type->slug))
            ->assertOk();
    }

    /**
     * @test
     */
    public function techSeesAddTypesAndEmployeeDoesnt(): void
    {
        /** @var User $tech */
        $tech = factory(User::class)->create();
        $tech->assignRole(UserRoles::TECHNICIAN);
        $tech->save();
        $this->actingAs($tech)
            ->get(route('home'))
            ->assertSeeText('Create a New Product Type');
        $this->actingAs($this->user)
            ->get(route('home'))
            ->assertDontSeeText('Create a New Product Type');
    }

    protected function setUp(): void
    {
        /** @var User $user */
        parent::setUp();
        $guest = factory(User::class)->make();
        $user = factory(User::class)->create();
        $user->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $user->save();
        $this->user = $user;
        $this->guest = $guest;
    }
}
