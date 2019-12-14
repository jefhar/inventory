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
use Illuminate\Support\Str;
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
        $this->actingAs($this->user)
            ->get(route(TypesController::SHOW_NAME, $type->slug))
            ->assertOk()
            ->assertSeeText($type->form);
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

    /**
     * @test
     */
    public function typeCreatePageExistsAndIsAccessible(): void
    {
        $this->actingAs($this->guest)
            ->get(route(TypesController::CREATE_NAME))
            ->assertForbidden();

        $this->withoutExceptionHandling()->actingAs($this->user)
            ->get(route(TypesController::CREATE_NAME))
            ->assertOk()
            ->assertSeeText('New Product Form');
    }

    /**
     * @test
     */
    public function typeIndexAPIExistsAndIsAccessible(): void
    {
        $type = factory(Type::class)->create();
        $this->actingAs($this->guest)
            ->get(route(TypesController::INDEX_NAME))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->get(route(TypesController::INDEX_NAME))
            ->assertOk()
            ->assertSeeText($type->name);
    }

    /**
     * @test
     */
    public function typeStoreExistsAndStoresForAuthorizedPerson(): void
    {
        $type = factory(Type::class)->make();
        $this->actingAs($this->guest)
            ->post(
                route(TypesController::STORE_NAME),
                [
                    Type::FORM => $type->form,
                    Type::NAME => $type->name,

                ]
            )
            ->assertForbidden();
        $this->actingAs($this->user)
            ->post(
                route(TypesController::STORE_NAME),
                [
                    Type::FORM => $type->form,
                    Type::NAME => $type->name,

                ]
            )->assertCreated();
        $this->assertDatabaseHas(
            Type::TABLE,
            [
                Type::FORM => $type->form,
                Type::NAME => Str::title($type->name),
            ]
        );
    }

    /**
     * @test
     */
    public function canDestroyForm()
    {
        $type = factory(Type::class)->create();
        $this
            ->actingAs($this->guest)
            ->delete(route(TypesController::DESTROY_NAME, $type))
            ->assertForbidden();

        // Destroyed Type returns OK
        $this
            ->actingAs($this->user)
            ->delete(route(TypesController::DESTROY_NAME, $type))
            ->assertOk();
        $this->assertSoftDeleted($type);
        // Destroyed non-existing Type returns 404
        $this->actingAs($this->user)
            ->delete(route(TypesController::DESTROY_NAME, $type))
            ->assertNotFound();
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
