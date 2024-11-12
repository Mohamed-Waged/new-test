<?php

namespace Modules\Coupons\Repositories;

use File;
use Excel;
use Exception;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Imageable;
use Illuminate\Support\Str;
use App\Constants\GlobalConstants;
use App\Repositories\BaseRepository;
use Modules\Roles\Entities\Permission;
use Modules\Coupons\Entities\Couponable;
use Modules\Coupons\Exports\CouponExport;
use Illuminate\Database\Eloquent\Builder;
use Modules\Coupons\Http\Resources\CouponResource;
use Modules\Coupons\Http\Resources\CouponMobileResource;
use Modules\Coupons\Http\Resources\CouponKeyValueResource;
use Modules\Coupons\Repositories\Contracts\CouponsRepositoryInterface;

class CouponsRepository extends BaseRepository implements CouponsRepositoryInterface
{
    protected $model;

    /**
     * @param Couponable $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Couponable $model)
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
        $rows = $this->buildQuery($request)->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        return [
            'rows'          => CouponResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('coupons')
        ];
    }

     /**
     * @param  mixed $request
     * @return Builder
     * @author Mohamed Elfeky <mohamed.elfeky@gatetechs.com>
     */
    public function buildQuery($request): Builder
    {
        return $this->model
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
            });
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

            $row                    = (!empty($id)) ? $this->model::find(decrypt($id)) : new $this->model;

            // translations
            foreach (['en', 'ar'] as $locale) {
                $row->translateOrNew($locale)->title = $request[$locale]['title'];
                $row->translateOrNew($locale)->body  = $request[$locale]['body'] ?? NULL;
            }

            $row->lecturer_id       = $request['lecturer']['id'];
            $row->couponeable_id    = $request['couponeableId'];
            $row->couponeable_type  = $request['couponeableType'];
            $row->coupon_count      = (int)$request['couponCount'];
            $row->coupon_percentage = (int)$request['couponPercentage'];
            $row->slug              = Str::slug($request['en']['title'], '-');
            $row->sort              = (int)$request['sort'];
            $row->is_active         = Helper::getStatusId($request['status']);
            $row->save();

            // Imageble
            $row->image()->delete();
            if (!empty($request['imageBase64'])) {
                if (!Str::contains($request['imageBase64'], [Imageable::contains()])) {
                    $image = Imageable::uploadImage($request['imageBase64'], 'coupons');
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
     * @param mixed $coupon
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($coupon): array
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->whereIsActive(true)
            ->whereHas('translations')
            ->when(!empty($coupon), function ($query) use ($coupon) {
                if (is_numeric($coupon)) {
                    $query->whereId($coupon);
                } else {
                    $query->whereSlug($coupon);
                    $query->orWhere('id', decrypt($coupon));
                }
            })
            ->first();

            return [
                'row' => Helper::isMobileDevice()
                    ? new CouponMobileResource($row)
                    : new CouponResource($row)
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
                // Mark the coupon as deleted by appending a deleted-at-timestamp to the slug
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
        $fileName   = 'coupons-' . now()->format('d-m-Y-H-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            if (!File::isDirectory($directory))
                File::makeDirectory(public_path($directory), 0777, true, true);

            // Optionally delete old files or handle file versioning
            File::deleteDirectory(public_path($directory));

            // Store the export
            Excel::store(new CouponExport($request), $path, 'public');

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
            ->whereHas('translations')
            ->latest('id')
            ->get();

        return [
            'rows' => CouponKeyValueResource::collection($rows)
        ];
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function list($request): array
    {
        $rows = $this->buildQuery($request)->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        return [
            'rows'      => CouponMobileResource::collection($rows),
            'paginate'  => Helper::paginate($rows)
        ];
    }
}
