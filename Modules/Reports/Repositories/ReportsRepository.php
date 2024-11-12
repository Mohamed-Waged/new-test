<?php

namespace Modules\Reports\Repositories;

use File;
use Excel;
use Helper;
use Exception;
use Carbon\Carbon;
use App\Models\Imageable;
use Illuminate\Support\Str;
use Modules\Pages\Entities\Page;
use App\Constants\GlobalConstants;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Modules\Roles\Entities\Permission;
use Modules\Reports\Exports\ReportExport;
use Modules\Reports\Http\Resources\ReportResource;
use Modules\Reports\Http\Resources\ReportMobileResource;
use Modules\Reports\Repositories\Contracts\ReportsRepositoryInterface;

class ReportsRepository extends BaseRepository implements ReportsRepositoryInterface
{
    protected $model;

    /**
     * @param Page $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Page $model)
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
            ->whereHas('translations')
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->whereTranslationLike('title', '%' . $request['search'] . '%');
            })
            ->when(!empty($request['date']), function ($query) use ($request) {
                $query->whereDate('created_at', $request['date']);
            })
            ->when(!empty($request['status']), function ($query) use ($request) {
                $query->whereStatus((strtolower($request['status']) == 'active') ? true : false);
            })
            ->when(!empty($request['sortBy']), function ($query) use ($request) {
                $key   = $request['sortBy'][0]['key'];
                $value = $request['sortBy'][0]['order'];
                if ($key == 'title') {
                    $query->orderByTranslation('title', $value);
                } else {
                    $query->orderBy($key, $value);
                }
            }, function ($else) {
                $else->latest('id');
            })
            ->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        return [
            'rows'          => ReportResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('reports')
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

            $row                = (!empty($id)) ? $this->model::find(decrypt($id)) : new $this->model;

            //

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param mixed $report
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($report)
    {
        //
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function export($request): array
    {
        $fileName   = 'reports-' . now()->format('d-m-Y-H-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            if (!File::isDirectory($directory))
                File::makeDirectory(public_path($directory), 0777, true, true);

            Excel::store(new ReportExport($request), $path, 'public');

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
}
