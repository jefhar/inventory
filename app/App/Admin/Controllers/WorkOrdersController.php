<?php

namespace App\Admin\Controllers;

use App\Admin\DataTransferObjects\ClientObject;
use App\Admin\DataTransferObjects\PersonObject;
use App\Admin\Requests\WorkOrderStoreRequest;
use Domain\WorkOrders\Actions\WorkOrdersStoreAction;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public const SHOW_NAME = 'workorders.show';
    public const STORE_NAME = 'workorders.store';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @noinspection PhpInconsistentReturnPointsInspection
     * @codeCoverageIgnore
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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

        if (empty($validated[Person::FIRST_NAME]) || (empty($validated[Person::LAST_NAME]))) {
            $personObject = null;
        } else {
            $personObject = PersonObject::fromRequest($request->validated());
        }

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
     * @param WorkOrder $workOrder
     * @return WorkOrder
     * @codeCoverageIgnore
     */
    public function edit(WorkOrder $workOrder): WorkOrder
    {
        return $workOrder;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @noinspection PhpInconsistentReturnPointsInspection
     * @codeCoverageIgnore
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @noinspection PhpInconsistentReturnPointsInspection
     * @codeCoverageIgnore
     */
    public function destroy($id)
    {
        //
    }
}
