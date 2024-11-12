<?php

namespace Modules\Users\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Users\Http\Requests\StoreRequest;
use Modules\Users\Http\Requests\UpdateRequest;
use Modules\Users\Repositories\Contracts\UsersRepositoryInterface;

class UsersController extends Controller implements UsersRepositoryInterface
{
    protected $repository;

    /**
     * @param UsersRepositoryInterface $repository
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(UsersRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->repository->index($request->input());
        return response()->json(['data' => $data], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRequest $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $response = $this->repository->createOrUpdate($request->validated());
            if ($response === true) {
                $data = ['message' => trans('app.users.created')];
                return response()->json(['data' => $data], 201);
            } else {
                $data = ['message' => trans('app.unableToCreate') . $response];
                return response()->json(['data' => $data], 500);
            }
        } catch (Exception $e) {
            $data = ['message' => trans('app.somethingWentWrong') . $e->getMessage()];
            return response()->json(['data' => $data], 500);
        }
    }

    /**
     * Show the specified resource.
     * @param mixed $page
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function show($page): JsonResponse
    {
        $data = $this->repository->find($page);
        return response()->json(['data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateRequest $request
     * @param mixed $id
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $response = $this->repository->createOrUpdate($request->validated(), $id);
            if ($response === true) {
                $data = ['message' => trans('app.users.updated')];
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
     * @param mixed $id
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function destroy($id): JsonResponse
    {
        try {
            $response = $this->repository->destroy($id);
            if ($response === true) {
                $data = ['message' => trans('app.users.deleted')];
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

    /**
     * Display a listing key Values of the resource.
     * @param Request $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function keyValue(Request $request): JsonResponse
    {
        $data = $this->repository->keyValue($request->input());
        return response()->json(['data' => $data], 200);
    }
}
