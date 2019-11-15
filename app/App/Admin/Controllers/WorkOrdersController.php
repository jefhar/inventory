<?php

namespace App\Admin\Controllers;

use App\Admin\DataTransferObjects\ClientObject;
use App\Admin\DataTransferObjects\PersonObject;
use App\Admin\Requests\WorkOrderStoreRequest;
use Domain\WorkOrders\Actions\WorkOrdersStoreAction;
use Domain\WorkOrders\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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
        abort_unless(
            Auth::user()->hasPermissionTo(self::CREATE_NAME),
            Response::HTTP_UNAUTHORIZED,
            Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
        );

        return view('workorders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkOrderStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(WorkOrderStoreRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated[Person::FIRST_NAME]) || (empty($validated[Person::LAST_NAME]))) {
            $personObject = null;
        } else {
            $personObject = PersonObject::fromRequest($request->validated());
        }

        $client = WorkOrdersStoreAction::execute(ClientObject::fromRequest($validated), $personObject);

        return redirect()->route(WorkOrdersController::SHOW_NAME, $client);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
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
     */
    public function destroy($id)
    {
        //
    }
}
