<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Controllers;

use App\Admin\Controllers\Controller;
use Domain\WorkOrders\Client;

class ClientsController extends Controller
{
    public const SHOW_PATH = '/clients/{client}';
    public const SHOW_NAME = 'clients.show';

    public function show(Client $client)
    {
        return $client->company_name . ' WorkOrder list';
    }
}
