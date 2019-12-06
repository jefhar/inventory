<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Types\Controllers;

use App\Admin\Controllers\Controller;
use Domain\Products\Models\Type;
use Illuminate\Http\Request;

class TypesController extends Controller
{
    public const SHOW_NAME = 'types.show';
    public const SHOW_PATH = 'types/{type}';

    /**
     * @param Request $request
     * @param Type $type
     */
    public function show(Request $request, Type $type)
    {
    }
}
