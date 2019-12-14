<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Types\Controllers;

use App\Admin\Controllers\Controller;
use App\Products\DataTransferObject\TypeStoreObject;
use App\Types\Requests\TypeStoreRequest;
use Domain\Products\Actions\TypeStoreAction;
use Domain\Products\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Class TypesController
 *
 * @package App\Types\Controllers
 */
class TypesController extends Controller
{
    public const CREATE_NAME = 'types.create';
    public const CREATE_PATH = '/types/create';
    public const DESTROY_NAME = 'types.destroy';
    public const DESTROY_PATH = '/types/{type}';
    public const INDEX_NAME = 'types.index';
    public const INDEX_PATH = '/types/';
    public const SHOW_NAME = 'types.show';
    public const SHOW_PATH = '/types/{type}';
    public const STORE_NAME = 'types.store';
    public const STORE_PATH = '/types';

    /**
     * @param Type $type
     * @return JsonResponse
     */
    public function show(Type $type): JsonResponse
    {
        return response()->json(json_decode($type->form, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @return View
     */
    public function create(): View
    {
        return view('types.create');
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Type::all());
    }

    /**
     * @param TypeStoreRequest $request
     * @return Type
     */
    public function store(TypeStoreRequest $request): Type
    {
        $typeStoreObject = TypeStoreObject::fromRequest($request->validated());

        return TypeStoreAction::execute($typeStoreObject);
    }

    /**
     * @param Type $type
     * @throws \Exception
     */
    public function destroy(Type $type): void
    {
        $type->delete();
    }
}
