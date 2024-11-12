<?php

namespace Modules\Messages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Messages\Http\Requests\StoreRequest;
use Modules\Messages\Http\Requests\UpdateRequest;
use Modules\Messages\Repositories\Contracts\MessagesRepositoryInterface;

class MessagesController extends Controller
{
    protected $repository;

    public function __construct(MessagesRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

     /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $data = $this->repository->index($request->input());
        return response()->json(['data' => $data], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreRequest $request)
    {
        try {
            $response = $this->repository->createOrUpdate($request->validated());
            if ($response === true) {
                $data = ['message' => trans('app.messages.created')];
                return response()->json(['data' => $data], 201);
            } else {
                $data = ['message' => trans('app.unableToCreate') . $response];
                return response()->json(['data' => $data], 500);
            }
        } catch (\Exception $e) {
            $data = ['message' => trans('app.somethingWentWrong') . $e->getMessage()];
            return response()->json(['data' => $data], 500);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = $this->repository->find($id);
        return response()->json(['data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            $response = $this->repository->createOrUpdate($request->validated(), $id);
            if ($response === true) {
                $data = ['message' => trans('app.messages.updated')];
                return response()->json(['data' => $data], 200);
            } else {
                $data = ['message' => trans('app.unableToUpdate') . $response];
                return response()->json(['data' => $data], 500);
            }
        } catch (\Exception $e) {
            $data = ['message' => trans('app.somethingWentWrong') . $e->getMessage()];
            return response()->json(['data' => $data], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $response = $this->repository->destroy($id);
            if ($response === true) {
                $data = ['message' => trans('app.messages.deleted')];
                return response()->json(['data' => $data], 200);
            } else {
                $data = ['message' => trans('app.unableToDelete') . $response];
                return response()->json(['data' => $data], 500);
            }
        } catch (\Exception $e) {
            $data = ['message' => trans('app.somethingWentWrong') . $e->getMessage()];
            return response()->json(['data' => $data], 500);
        }
    }

    /**
     * export the specified resource from storage.
     * @param Request $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $response = $this->repository->export($request->input());
            if ($response['status'] === true) {
                $data = ['message' => trans('app.fileExported'), 'path' => $response['path']];
                return response()->json(['data' => $data], 200);
            } else {
                $data = ['message' => trans('app.unableToExport') . $response['message']];
                return response()->json(['data' => $data], 500);
            }
        } catch (\Exception $e) {
            $data = ['message' => trans('app.somethingWentWrong') . $e->getMessage()];
            return response()->json(['data' => $data], 500);
        }
    }
}
