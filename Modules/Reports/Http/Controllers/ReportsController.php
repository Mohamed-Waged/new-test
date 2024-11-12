<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Reports\Repositories\Contracts\ReportsRepositoryInterface;

class ReportsController extends Controller
{
    protected $repository;

    /**
     * @param ReportsRepositoryInterface $repository
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(ReportsRepositoryInterface $repository)
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
}
