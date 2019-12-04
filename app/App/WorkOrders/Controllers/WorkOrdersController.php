<?php

namespace App\WorkOrders\Controllers;

use App\Admin\Controllers\Controller;
use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
use App\WorkOrders\DataTransferObjects\WorkOrderUpdateObject;
use App\WorkOrders\Requests\WorkOrderStoreRequest;
use App\WorkOrders\Requests\WorkOrderUpdateRequest;
use Domain\WorkOrders\Actions\WorkOrdersStoreAction;
use Domain\WorkOrders\Actions\WorkOrdersUpdateAction;
use Domain\WorkOrders\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WorkOrdersController
 *
 * @package App\Admin\Controllers
 */
class WorkOrdersController extends Controller
{
    public const CREATE_NAME = 'workorders.create';
    public const CREATE_PATH = '/workorders/create';
    public const EDIT_NAME = 'workorders.edit';
    public const INDEX_NAME = 'workorders.index';
    public const SHOW_NAME = 'workorders.show';
    public const STORE_NAME = 'workorders.store';
    public const UPDATE_NAME = 'workorders.update';

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
            $workOrders = WorkOrder::paginate(15);
            $showLocked = 'yes';
        } else {
            $workOrders = WorkOrder::where(WorkOrder::IS_LOCKED, false)->paginate(15);
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
     */
    public function store(WorkOrderStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $personObject = PersonObject::fromRequest($request->validated());

        $workOrder = WorkOrdersStoreAction::execute(ClientObject::fromRequest($validated), $personObject);

        return response()
            ->json(['workorder_id' => $workOrder->id, 'created' => true], Response::HTTP_CREATED)
            ->header(
                'Location',
                route(self::SHOW_NAME, ['workorder' => $workOrder])
            );
    }

    /**
     * Display the specified resource.
     *
     * @param WorkOrder $workorder
     * @return WorkOrder
     * @codeCoverageIgnore
     */
    public function show(WorkOrder $workorder): WorkOrder
    {
        return $workorder;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param WorkOrder $workorder
     * @return View
     */
    public function edit(WorkOrder $workorder): View
    {
        return view('workorders.edit')->with(['workOrder' => $workorder]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WorkOrderUpdateRequest $request
     * @param WorkOrder $workorder
     * @return JsonResponse
     */
    public function update(WorkOrderUpdateRequest $request, WorkOrder $workorder): JsonResponse
    {
        $workOrderObject = WorkOrderUpdateObject::fromRequest($request->validated());
        $workOrderAction = WorkOrdersUpdateAction::execute(
            $workorder,
            $workOrderObject
        );

        return response()->json($workOrderAction);
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
