<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Controllers\AjaxSearchController;
use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\WorkOrders\Client;
use Tests\TestCase;

class AjaxSearchTest extends TestCase
{
    private User $unauthorizedUser;
    private User $authorizedUser;

    public function setUp(): void
    {
        /** @var User $authorizedUser */
        parent::setUp();
        $unauthorizedUser = factory(User::class)->create();
        $authorizedUser = factory(User::class)->create();
        $authorizedUser->assignRole(UserRoles::EMPLOYEE);
        $this->unauthorizedUser = $unauthorizedUser;
        $this->authorizedUser = $authorizedUser;
    }

    /**
     * @test
     */
    public function anonymousIsUnauthorized(): void
    {
        $this->get(route(AjaxSearchController::SHOW_NAME, ['field' => Client::COMPANY_NAME]))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function unauthorizedIsUnauthorized(): void
    {
        $this->withExceptionHandling()->actingAs($this->unauthorizedUser);
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                [
                    'field' => Client::COMPANY_NAME,
                ]
            )
        )
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function authorizedIsOK(): void
    {
        $this->actingAs($this->authorizedUser)->withoutExceptionHandling();
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                ['field' => Client::COMPANY_NAME]
            )
        )->assertOK();
    }

    /**
     * @test
     */
    public function knownFieldIsOK(): void
    {
        $this->actingAs($this->authorizedUser);
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                [
                    'field' => Client::COMPANY_NAME,
                ]
            )
        )->assertOk();
    }

}
