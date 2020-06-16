<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Controllers;

use App\Admin\Controllers\Controller;
use Domain\WorkOrders\Models\Client;
use Illuminate\View\View;

/**
 * Class ClientsController
 *
 * @package App\WorkOrders\Controllers
 */
class ClientsController extends Controller
{
    public const SHOW_PATH = '/clients/{client}';
    public const SHOW_NAME = 'clients.show';

    /**
     * @param Client $client
     * @return View
     */
    public function show(Client $client): View
    {
        return view('clients.show')->with(['client' => $client]);
    }
}
