<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\AjaxSearch\Controllers;

use App\Admin\Controllers\Controller;
use App\AjaxSearch\DataTransferObjects\AjaxSearchObject;
use App\AjaxSearch\Requests\AjaxSearchRequest;
use App\AjaxSearch\Resources\AjaxSearchCollectionResource;
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
     * @param string $field
     * @param AjaxSearchRequest $request
     * @return AjaxSearchCollectionResource
     */
    public function show(string $field, AjaxSearchRequest $request): AjaxSearchCollectionResource
    {
        $ajaxSearchObject = AjaxSearchObject::fromRequest($field, $request->validated());

        $results = AjaxSearchAction::findBy($ajaxSearchObject);

        return new AjaxSearchCollectionResource($results);
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
