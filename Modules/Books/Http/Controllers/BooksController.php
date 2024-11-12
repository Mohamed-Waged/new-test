<?php

namespace Modules\Books\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Books\Http\Requests\StoreRequest;
use Modules\Books\Http\Requests\UpdateRequest;
use Modules\Books\Repositories\Contracts\BooksRepositoryInterface;

class BooksController extends Controller
{
    protected $repository;

    /**
     * @param BooksRepositoryInterface $repository
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(BooksRepositoryInterface $repository)
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
                $data = ['message' => trans('app.books.created')];
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
     * @param mixed $book
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function show($book): JsonResponse
    {
        $data = $this->repository->find($book);
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
                $data = ['message' => trans('app.books.updated')];
                return response()->json(['data' => $data], 200);
            } else {
                $data = ['message' => trans('app.unableToUpdate') . $response];
                return response()->json(['data' => $data], 500);
            }
        } catch (Exception $e) {
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
                $data = ['message' => trans('app.books.deleted')];
                return response()->json(['data' => $data], 200);
            } else {
                $data = ['message' => trans('app.unableToDelete') . $response];
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
        } catch (Exception $e) {
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

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->repository->list($request->input());
        return response()->json(['data' => $data], 200);
    }
}
