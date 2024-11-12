<?php

namespace Modules\Courses\Repositories;

use File;
use Excel;
use Helper;
use Exception;
use Carbon\Carbon;
use App\Models\Imageable;
use Illuminate\Support\Str;
use App\Constants\GlobalConstants;
use App\Repositories\BaseRepository;
use Modules\Courses\Entities\Course;
use Modules\Roles\Entities\Permission;
use Modules\Courses\Exports\CourseExport;
use Modules\Courses\Http\Resources\CourseResource;
use Modules\Courses\Http\Resources\CourseMobileResource;
use Modules\Courses\Http\Resources\CourseKeyValueResource;
use Modules\Courses\Repositories\Contracts\CoursesRepositoryInterface;

class CoursesRepository extends BaseRepository implements CoursesRepositoryInterface
{
    protected $model;

    /**
     * @param Course $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Course $model)
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
            'rows'          => CourseResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('courses')
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
                    $image = Imageable::uploadImage($request['imageBase64'], 'courses');
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
     * @param mixed $course
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($course): array
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->whereIsActive(true)
            ->when(!empty($course), function ($query) use ($course) {
                if (is_numeric($course)) {
                    $query->whereId($course);
                } else {
                    $query->whereSlug($course);
                    $query->orWhere('id', decrypt($course));
                }
            })
            ->first();

        return [
            'row' => new CourseMobileResource($row)
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
                // Mark the course as deleted by appending a deleted-at-timestamp to the slug
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
        $fileName   = 'courses-' . now()->format('d-m-Y-H-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            if (!File::isDirectory(public_path($directory)))
                File::makeDirectory(public_path($directory), 0777, true, true);

            Excel::store(new CourseExport($request), $path, 'public');

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
            'rows' => CourseKeyValueResource::collection($rows)
        ];
    }
}
