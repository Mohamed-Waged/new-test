<?php

namespace Modules\Lecturers\Repositories;

use File;
use Excel;
use Helper;
use Exception;
use Carbon\Carbon;
use App\Models\Imageable;
use Illuminate\Support\Str;
use App\Constants\GlobalConstants;
use App\Repositories\BaseRepository;
use Modules\Roles\Entities\Permission;
use Modules\Lecturers\Entities\Lecturer;
use Modules\Lecturers\Exports\LecturerExport;
use Modules\Lecturers\Http\Resources\LecturerResource;
use Modules\Lecturers\Http\Resources\LecturerMobileResource;
use Modules\Lecturers\Http\Resources\LecturerKeyValueResource;
use Modules\Lecturers\Repositories\Contracts\LecturersRepositoryInterface;

class LecturersRepository extends BaseRepository implements LecturersRepositoryInterface
{
    protected $model;

    /**
     * @param Lecturer $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Lecturer $model)
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
                $query->whereIsActive((strtolower($request['status']) == 'active') ? true : false);
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
            'rows'          => LecturerResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('lecturers')
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

            // translations
            foreach (['en', 'ar'] as $locale) {
                $row->translateOrNew($locale)->title = $request[$locale]['title'];
                $row->translateOrNew($locale)->body  = $request[$locale]['body'] ?? NULL;
            }


            $row->slug          = Str::slug($request['en']['title'], '-');
            $row->sort          = (int)$request['sort'];
            $row->is_active     = Helper::getStatusId($request['status']);
            $row->save();

            // Imageble
            $row->image()->delete();
            if (!empty($request['imageBase64'])) {
                if (!Str::contains($request['imageBase64'], [Imageable::contains()])) {
                    $image = Imageable::uploadImage($request['imageBase64'], 'lecturers');
                } else {
                    $image = explode('/', $request['imageBase64']);
                    $image = end($image);
                }
                $row->image()->create(['url' => $image]);
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param mixed $lecturer
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($lecturer): array
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->whereIsActive(true)
            ->when(!empty($lecturer), function ($query) use ($lecturer) {
                if (is_numeric($lecturer)) {
                    $query->whereId($lecturer);
                } else {
                    $query->whereSlug($lecturer);
                    $query->orWhere('id', decrypt($lecturer));
                }
            })
            ->first();

        return [
            'row' => new LecturerMobileResource($row)
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
            $destroy = $this->model->find(decrypt($id));

            if ($destroy) {
                // Mark the lecturer as deleted by appending a deleted-at-timestamp to the slug
                $destroy->slug      .= '-deleted-at-' . Carbon::now()->timestamp;
                $destroy->deleted_by = auth('api')->user()->id;
                $destroy->deleted_at = Carbon::now();
                $destroy->save();
            }

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
        $fileName   = 'lecturers-' . now()->format('d-m-Y-H-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            if (!File::isDirectory($directory))
                File::makeDirectory(public_path($directory), 0777, true, true);

            Excel::store(new LecturerExport($request), $path, 'public');

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
            'rows' => LecturerKeyValueResource::collection($rows)
        ];
    }
}
