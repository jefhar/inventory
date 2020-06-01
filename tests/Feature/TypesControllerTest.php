<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Types\Controllers\TypeController;
use Domain\Products\Models\Type;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\FullUsers;

/**
 * Class TypesControllerTest
 *
 * @package Tests\Feature
 */
class TypesControllerTest extends TestCase
{
    use FullUsers;

    /**
     * @test
     */
    public function unauthorizedTypesControllerIsRedirected(): void
    {
        $type = factory(Type::class)->create();
        $this
            ->get(route(TypeController::SHOW_NAME, $type->slug))
            ->assertRedirect();
    }

    /**
     * @test
     */
    public function authorizedTypesControllerIsOk(): void
    {
        /** @var Type $type */
        $type = factory(Type::class)->create();
        $this->actingAs($this->createEmployee())
            ->get(route(TypeController::SHOW_NAME, $type->slug))
            ->assertOk()
            ->assertSeeText($type->form, false);
    }

    /**
     * @test
     */
    public function techSeesAddTypesAndEmployeeDoesntOnDashboard(): void
    {
        $this->withoutMix()
            ->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route('home'))
            ->assertSeeText('Create a New Product Type');
        $this->actingAs($this->createEmployee())
            ->get(route('home'))
            ->assertDontSeeText('Create a New Product Type');
    }

    /**
     * @test
     */
    public function typeCreatePageExistsAndIsAccessible(): void
    {
        $this->withoutMix();
        $type = factory(Type::class)->create();
        $this->get(route(TypeController::CREATE_NAME))
            ->assertRedirect();

        $this->actingAs($this->createEmployee())
            ->get(route(TypeController::CREATE_NAME))
            ->assertOk()
            ->assertSeeText('Create New Product Type')
            ->assertSeeText($type->name)
            ->assertSee($type->slug);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function typeIndexApiExistsAndIsAccessible(): void
    {
        $type = factory(Type::class)->create();
        $this->get(route(TypeController::INDEX_NAME))
            ->assertRedirect();

        $this->actingAs($this->createEmployee())
            ->get(route(TypeController::INDEX_NAME))
            ->assertOk()
            ->assertSeeText(json_encode($type->name, JSON_THROW_ON_ERROR), false);
    }

    /**
     * @test
     */
    public function typeStoreExistsAndStoresForAuthorizedPerson(): void
    {
        $type = factory(Type::class)->make();
        $this->post(
            route(TypeController::STORE_NAME),
            [Type::FORM => $type->form, Type::NAME => $type->name,]
        )
            ->assertRedirect();
        $this->actingAs($this->createEmployee())
            ->post(
                route(TypeController::STORE_NAME),
                [Type::FORM => $type->form, Type::NAME => $type->name,]
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
    public function canDestroyForm(): void
    {
        $type = factory(Type::class)->create();
        $this
            ->delete(route(TypeController::DESTROY_NAME, $type))
            ->assertRedirect();

        // Destroyed Type returns OK
        $this
            ->actingAs($this->createEmployee())
            ->delete(route(TypeController::DESTROY_NAME, $type))
            ->assertOk();
        $this->assertSoftDeleted($type);
        // Destroyed non-existing Type returns 404
        $this
            ->delete(route(TypeController::DESTROY_NAME, $type))
            ->assertNotFound();
    }

    /**
     * @test
     */
    public function savingExistingFormWithoutForceReturnsAccepted(): void
    {
        $type = factory(Type::class)->create();
        $this->actingAs($this->createEmployee())
            ->post(
                route(TypeController::STORE_NAME),
                [Type::NAME => $type->name, Type::FORM => $type->form, 'force' => false,]
            )
            ->assertStatus(Response::HTTP_ACCEPTED);

        $this->actingAs($this->createEmployee())
            ->post(
                route(TypeController::STORE_NAME),
                [Type::NAME => $type->name, Type::FORM => $type->form,]
            )
            ->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * @test
     */
    public function savingExistingFormCanBeForced(): void
    {
        $type = factory(Type::class)->create();
        $this->actingAs($this->createEmployee())
            ->post(
                route(TypeController::STORE_NAME),
                [Type::NAME => $type->name, Type::FORM => $type->form, 'force' => true,]
            )
            ->assertOk();
    }
}
