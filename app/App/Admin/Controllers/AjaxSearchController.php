<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Permissions\UserPermissions;
use Domain\WorkOrders\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxSearchController extends Controller
{
    public const SHOW_NAME = 'ajaxsearch.show';
    public const SHOW_PATH = '/ajaxsearch/{field}';

    /**
     * @param Request $request
     * @param string $field
     * @return string
     */
    public function show(Request $request, string $field)
    {
        $availableFields = [
            Client::COMPANY_NAME => Client::COMPANY_NAME,
        ];
        $user = $request->user();
        if ($user === null) {
            abort(Response::HTTP_UNAUTHORIZED, Response::$statusTexts[Response::HTTP_UNAUTHORIZED]);
        }
        if ($user->cant(UserPermissions::IS_EMPLOYEE)) {
            abort(Response::HTTP_UNAUTHORIZED, Response::$statusTexts[Response::HTTP_UNAUTHORIZED]);
        }
        if (!array_key_exists($field, $availableFields)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }

        $searchString = $request->get('q', '');
        $options = \Domain\AjaxSearch\Actions\AjaxSearch::findBy($field, $searchString);

        return response()->json($options);
    }
}
