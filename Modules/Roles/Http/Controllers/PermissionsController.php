<?php

namespace Modules\Roles\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Roles\Repositories\Contracts\PermissionsRepositoryInterface;

class PermissionsController extends Controller
{
    protected $repository;

    /**
     * @param PermissionsRepositoryInterface $repository
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(PermissionsRepositoryInterface $repository)
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
        $data = $this->repository->index($request->all());
        return response()->json(['data' => $data], 200);
    }
}
