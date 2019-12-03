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
        $searchString = $request->get('q', '');
        $options = AjaxSearchAction::findBy($field, $searchString);

        return response()->json($options);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $results = AjaxSearchAction::findAll($request->get('q'));

        return response()->json($results);
    }
}
