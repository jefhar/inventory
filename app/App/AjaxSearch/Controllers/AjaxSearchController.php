<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\AjaxSearch\Controllers;

use App\Admin\Controllers\Controller;
use Domain\AjaxSearch\Actions\AjaxSearchAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AjaxSearchController
 *
 * @package App\AjaxSearch\Controllers
 */
class AjaxSearchController extends Controller
{
    public const SHOW_NAME = 'ajaxsearch.show';
    public const SHOW_PATH = '/ajaxsearch/{field}';
    public const INDEX_PATH = '/ajaxsearch';
    public const INDEX_NAME = 'ajaxsearch.index';

    /**
     * @param Request $request
     * @param string $field
     * @return JsonResponse
     */
    public function show(Request $request, string $field): JsonResponse
    {
        return response()->json(AjaxSearchAction::findBy($field, $request->get('q', '')));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(AjaxSearchAction::findAll($request->get('q')));
    }
}
