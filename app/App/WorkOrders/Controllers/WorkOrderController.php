<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Controllers;

use App\Admin\Controllers\Controller;
use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
use App\WorkOrders\DataTransferObjects\WorkOrderObject;
use App\WorkOrders\Requests\WorkOrderStoreRequest;
use App\WorkOrders\Requests\WorkOrderUpdateRequest;
use App\WorkOrders\Resources\WorkOrderUpdateResource;
use Domain\Products\Models\Type;
use Domain\WorkOrders\Actions\WorkOrdersStoreAction;
use Domain\WorkOrders\Actions\WorkOrdersUpdateAction;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WorkOrdersController
 *
 * @package App\Admin\Controllers
 */
class WorkOrderController extends Controller
{
    public const CREATE_NAME = 'workorders.create';
    public const CREATE_PATH = '/workorders/create';
    public const EDIT_NAME = 'workorders.edit';
    public const INDEX_NAME = 'workorders.index';
    public const PAGINATE_PER_PAGE = 15;
    public const SHOW_NAME = 'workorders.show';
    public const STORE_NAME = 'workorders.store';
    public const UPDATE_NAME = 'workorders.update';
    public const WORKORDER = 'workorder';

    /**
     * Display a listing of the resource.
     *
     * @codeCoverageIgnore
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $showLocked = 'no';
        if ($request->get('showlocked', $showLocked) === 'yes') {
            $workOrders = WorkOrder::paginate(self::PAGINATE_PER_PAGE);
            $showLocked = 'yes';
        } else {
            $workOrders = WorkOrder::where(WorkOrder::IS_LOCKED, false)->paginate(self::PAGINATE_PER_PAGE);
            $showLocked = 'no';
        }

        return view('workorders.index')->with(['workOrders' => $workOrders, 'showlocked' => $showLocked]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('workorders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkOrderStoreRequest $request
     * @return JsonResponse
     * @todo: change luhn response to id response -> through JS.
     */
    public function store(WorkOrderStoreRequest $request): JsonResponse
    {
        $workOrder = WorkOrdersStoreAction::execute(
            ClientObject::fromRequest($request->validated()),
            PersonObject::fromRequest($request->validated())
        );

        // Why does this send a response, a status, and a location?
        return response()
            ->json(['workorder_id' => $workOrder->luhn, 'created' => true], Response::HTTP_CREATED)
            ->header(
                'Location',
                route(self::SHOW_NAME, ['workorder' => $workOrder->luhn])
            );
    }

    /**
     * Display the specified resource.
     *
     * @param WorkOrder $workOrder
     * @return WorkOrder
     * @codeCoverageIgnore
     */
    public function show(WorkOrder $workOrder): WorkOrder
    {
        return $workOrder;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param WorkOrder $workorder
     * @return View
     */
    public function edit(WorkOrder $workorder): View
    {
        $workorder->load('products');
        $types = Type::select(Type::SLUG, Type::NAME)->orderBy(Type::SLUG)->get();

        return view('workorders.edit')->with(['workOrder' => $workorder, 'types' => $types]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WorkOrderUpdateRequest $request
     * @param WorkOrder $workorder
     * @return array
     */
    public function update(WorkOrderUpdateRequest $request, WorkOrder $workorder)
    {
        $personObject = PersonObject::fromRequest($request->validated());
        $clientObject = ClientObject::fromRequest($request->validated());
        $workOrderObject = WorkOrderObject::fromRequest($request->validated());

        $workOrderResource = new WorkOrderUpdateResource();

        return $workOrderResource->toArray(
            WorkOrdersUpdateAction::execute(
                $workorder,
                $workOrderObject,
                $clientObject,
                $personObject
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     * @codeCoverageIgnore
     */
    public function destroy($id)
    {
        //
    }
}
