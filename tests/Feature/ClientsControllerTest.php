<?php

namespace Tests\Feature;

use App\Admin\Permissions\UserPermissions;
use App\User;
use App\WorkOrders\Controllers\ClientsController;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Tests\TestCase;

class ClientsControllerTest extends TestCase
{
    /**
     * @test
     */
    public function clientsShowPageListsWorkOrders(): void
    {
        $person = factory(Person::class)->make();
        $client = factory(Client::class)->create();
        $client->person()->save($person);
        $client->company_name = "O'" . $client->company_name;
        $client->save();
        $user = factory(User::class)->create();
        $user->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $this->actingAs($user)
            ->get(route(ClientsController::SHOW_NAME, $client))
            ->assertSee(htmlspecialchars($client->company_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES));
    }
}
