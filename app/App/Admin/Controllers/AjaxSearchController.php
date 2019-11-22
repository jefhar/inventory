<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use Domain\AjaxSearch\Actions\AjaxSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AjaxSearchController extends Controller
{
    public const SHOW_NAME = 'ajaxsearch.show';
    public const SHOW_PATH = '/ajaxsearch/{field}';

    /**
     * @param Request $request
     * @param string $field
     * @return JsonResponse
     */
    public function show(Request $request, string $field): JsonResponse
    {
        $searchString = $request->get('q', '');
        $options = AjaxSearch::findBy($field, $searchString);

        return response()->json($options);
    }
}
