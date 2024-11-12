<?php

namespace Modules\Notifications\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Notifications\Http\Requests\StoreRequest;
use Modules\Notifications\Repositories\Contracts\NotificationsRepositoryInterface;

class NotificationsController extends Controller
{
    protected $repository;

    /**
     * @param NotificationsRepositoryInterface $repository
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(NotificationsRepositoryInterface $repository)
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
                $data = ['message' => trans('app.notifications.created')];
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
