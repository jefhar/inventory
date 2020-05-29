<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Types\Controllers\TypesController;
use App\Types\Requests\TypeStoreRequest;
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
        /** @var Type $type */
        $type = factory(Type::class)->create();
        $this
            ->get(route(TypesController::SHOW_NAME, $type->slug))
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
            ->get(route(TypesController::SHOW_NAME, $type->slug))
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
        $this->get(route(TypesController::CREATE_NAME))
            ->assertRedirect();

        $this->actingAs($this->createEmployee())
            ->get(route(TypesController::CREATE_NAME))
            ->assertOk()
            ->assertSeeText('Create New Product Type');
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function typeIndexApiExistsAndIsAccessible(): void
    {
        /** @var Type $type */
        $type = factory(Type::class)->create();
        $this->get(route(TypesController::INDEX_NAME))
            ->assertRedirect();

        $this->actingAs($this->createEmployee())
            ->get(route(TypesController::INDEX_NAME))
            ->assertOk()
            ->assertSeeText(json_encode($type->name, JSON_THROW_ON_ERROR), false);
    }

    /**
     * @test
     */
    public function typeStoreExistsAndStoresForAuthorizedPerson(): void
    {
        /** @var Type $type */
        $type = factory(Type::class)->make();
        $this->post(
            route(TypesController::STORE_NAME),
            [TypeStoreRequest::FORM => $type->form, TypeStoreRequest::NAME => $type->name,]
        )
            ->assertRedirect();
        $this->actingAs($this->createEmployee())
            ->post(
                route(TypesController::STORE_NAME),
                [TypeStoreRequest::FORM => $type->form, TypeStoreRequest::NAME => $type->name,]
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
            ->delete(route(TypesController::DESTROY_NAME, $type))
            ->assertRedirect();

        // Destroyed Type returns OK
        $this
            ->actingAs($this->createEmployee())
            ->delete(route(TypesController::DESTROY_NAME, $type))
            ->assertOk();
        $this->assertSoftDeleted($type);
        // Destroyed non-existing Type returns 404
        $this
            ->delete(route(TypesController::DESTROY_NAME, $type))
            ->assertNotFound();
    }

    /**
     * @test
     */
    public function savingExistingFormWithoutForceReturnsAccepted(): void
    {
        /** @var Type $type */
        $type = factory(Type::class)->create();
        $this->actingAs($this->createEmployee())
            ->post(
                route(TypesController::STORE_NAME),
                [
                    TypeStoreRequest::FORCE => false,
                    TypeStoreRequest::FORM => $type->form,
                    TypeStoreRequest::NAME => $type->name,
                ]
            )
            ->assertStatus(Response::HTTP_ACCEPTED);

        $this->actingAs($this->createEmployee())
            ->post(
                route(TypesController::STORE_NAME),
                [TypeStoreRequest::NAME => $type->name, TypeStoreRequest::FORM => $type->form,]
            )
            ->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * @test
     */
    public function savingExistingFormCanBeForced(): void
    {
        /** @var Type $type */
        $type = factory(Type::class)->create();
        $this->actingAs($this->createEmployee())
            ->post(
                route(TypesController::STORE_NAME),
                [
                    TypeStoreRequest::FORCE => true,
                    TypeStoreRequest::FORM => $type->form,
                    TypeStoreRequest::NAME => $type->name,
                ]
            )
            ->assertOk();
    }
}
