<?php

namespace Tests\Feature;

use App\Admin\Permissions\UserPermissions;
use App\User;
use App\WorkOrders\Controllers\ClientsController;
use Domain\WorkOrders\Client;
use Tests\TestCase;

class ClientsControllerTest extends TestCase
{
    /**
     * @test
     */
    public function clientsShowPageListsWorkOrders(): void
    {
        $client = factory(Client::class)->create();
        $user = factory(User::class)->create();
        $user->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $this->actingAs($user)
            ->get(route(ClientsController::SHOW_NAME, $client))
            ->assertSee($client->company_name);
    }
}
