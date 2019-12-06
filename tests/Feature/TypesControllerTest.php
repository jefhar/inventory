<?php

namespace Tests\Feature;

use App\Admin\Permissions\UserPermissions;
use App\User;
use Domain\Products\Models\Type;
use Tests\TestCase;

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
            ->get(route(\App\Types\Controllers\TypesController::SHOW_NAME, $type->slug))
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
            ->get(route(\App\Types\Controllers\TypesController::SHOW_NAME, $type->slug))
            ->assertOk();
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
