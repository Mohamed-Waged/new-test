<?php

namespace Modules\Consultations\Repositories;

use File;
use Excel;
use Helper;
use Exception;
use Carbon\Carbon;
use Modules\Consultations\Entities\Consultation;
use Modules\Roles\Entities\Permission;
use App\Repositories\BaseRepository;
use Modules\Consultations\Exports\ConsultationExport;
use Modules\Consultations\Http\Resources\ConsultationResource;
use Modules\Consultations\Http\Resources\ConsultationMobileResource;
use Modules\Consultations\Repositories\Contracts\ConsultationsRepositoryInterface;

class ConsultationsRepository extends BaseRepository implements ConsultationsRepositoryInterface
{
    protected $model;

    /**
     * @param Consultation $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Consultation $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function index($request): array
    {
        $rows = $this->model
            ->whereNULL('deleted_at')
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request['search'] . '%');
            })
            ->when(!empty($request['date']), function ($query) use ($request) {
                $query->whereDate('created_at', $request['date']);
            })
            ->when(!empty($request['status']), function ($query) use ($request) {
                $query->whereIsActive((strtolower($request['status']) == 'active') ? true : false);
            })
            ->when(!empty($request['sortBy']), function ($query) use ($request) {
                $key   = $request['sortBy'][0]['key'];
                $value = $request['sortBy'][0]['order'];
                $query->orderBy($key, $value);
            }, function ($else) {
                $else->latest('id');
            })
            ->paginate($request['paginate'] ?? 10);

        return [
            'rows'          => ConsultationResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('consultations')
        ];
    }

    /**
     * @param array $request
     * @param string|null $id
     * @return mixed
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function createOrUpdate($request, $id = NULL): mixed
    {
        try {

            //

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param mixed $consultation
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($consultation): array
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->whereIsActive(true)
            ->when(!empty($consultation), function ($query) use ($consultation) {
                if (is_numeric($consultation)) {
                    $query->whereId($consultation);
                } else {
                    $query->whereSlug($consultation);
                    $query->orWhere('id', decrypt($consultation));
                }
            })
            ->first();

        return [
            'row' => Helper::isMobileDevice()
                ? new ConsultationMobileResource($row)
                : new ConsultationResource($row)
        ];
    }

    /**
     * @param string $id
     * @return mixed
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function destroy($id): mixed
    {
        try {
            $this->model->where('id', decrypt($id))->update([
                'deleted_by'    => auth('api')->user()->id,
                'deleted_at'    => Carbon::now()
            ]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function export($request): array
    {
        $fileName   = 'consultation-' . date('d-m-Y-h-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            if (!File::isDirectory($directory)) {
                File::makeDirectory(public_path($directory), 0777, true, true);
            }

            Excel::store(new ConsultationExport($request), $path, 'public');

            return [
                'status'    => true,
                'path'      => request()->root() . '/' . $path,
            ];
        } catch (Exception $e) {
            return [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        }
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function keyValue(): array
    {
        $rows = $this->model
            ->whereNULL('deleted_at')
            ->whereIsActive(true)
            ->latest('id')
            ->get();

        return [
            'rows' => ConsultationKeyValueResource::collection($rows)
        ];
    }
}
