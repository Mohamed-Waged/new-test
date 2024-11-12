<?php

namespace Modules\Settings\Repositories;

use Helper;
use Carbon\Carbon;
use App\Models\Imageable;
use Illuminate\Support\Str;
use App\Constants\GlobalConstants;
use App\Libraries\IsoCountryCodes;
use App\Repositories\BaseRepository;
use Modules\Roles\Entities\Permission;
use Modules\Settings\Entities\Setting;
use Modules\Settings\Http\Resources\SettingResource;
use Modules\Settings\Http\Resources\SettingShowResource;
use Modules\Settings\Http\Resources\SettingMobileResource;
use Modules\Settings\Http\Resources\SettingKeyValueResource;
use Modules\Settings\Repositories\Contracts\SettingsRepositoryInterface;

class SettingsRepository extends BaseRepository implements SettingsRepositoryInterface
{
    protected $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function index($request)
    {
        $rows = $this->model
            ->whereNULL('deleted_at')
            ->whereHas('translations')
            ->when(!empty($request['parentId']), function ($query) use ($request) {
                $query->whereParentId(decrypt($request['parentId']));
            }, function ($else) {
                $else->whereNULL('parent_id');
            })
            ->when(!empty($request['user']) && is_array($request['user']), function ($query) use ($request) {
                $query->whereUserId($request['user']['id']);
            })
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->whereTranslationLike('title', '%' . $request['search'] . '%');
            })
            ->when(!empty($request['date']), function ($query) use ($request) {
                $query->whereDate('created_at', $request['date']);
            })
            ->when(!empty($request['status']), function ($query) use ($request) {
                $query->whereIsActive((strtolower($request['status']) == 'active') ? true : false);
            })
            ->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        return [
            'rows'        => SettingResource::collection($rows),
            'paginate'    => Helper::paginate($rows),
            'permissions' => Permission::getAccessPermissions('settings')
        ];
    }

    public function createOrUpdate($request, $id = NULL)
    {
        try {
            $row                    = (!empty($id)) ? $this->model::find(decrypt($id)) : new $this->model;

            // translations
            foreach (['en', 'ar'] as $locale) {
                $row->translateOrNew($locale)->title = $request[$locale]['title'];
                $row->translateOrNew($locale)->body = $request[$locale]['body'];
            }

            $row->lecturer_id       = $request['lecturer']['id'] ?? NULL;
            $row->parent_id         = $request['parentId'] ? decrypt($request['parentId']) : NULL;
            $row->slug              = Str::slug($request['en']['title'], '_');
            $row->value             = $request['value'] ?? NULL;
            $row->icon              = $request['icon'] ?? NULL;
            $row->sort              = (int)$request['sort'];
            $row->is_active         = Helper::getStatusId($request['status']);
            $row->save();

            // Imageble
            $row->image()->delete();
            if (isset($request['imageBase64']) && $request['imageBase64']) {
                if (!Str::contains($request['imageBase64'], [Imageable::contains()])) {
                    $image = Imageable::uploadImage($request['imageBase64'], 'settings');
                } else {
                    $image = explode('/', $request['imageBase64']);
                    $image = end($image);
                }
                $row->image()->create(['url' => $image]);
            }

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function find($request, $slug)
    {
        if ($slug != 'appSettings') {
            $parent = $this->model->where('slug', $slug)->first();
            $rows   = $this->model->whereNULL('deleted_at')
                ->whereStatus(true)
                ->when(!empty($request['parentId']), function ($query) use ($request, $parent) {
                    $query->where('parent_id', $request['parentId']);
                }, function ($else) use ($parent) {
                    $else->where('parent_id', $parent->id);
                })
                ->get();
            return [
                'rows' => SettingShowResource::collection($rows)
            ];
        }
    }

    public function destroy($id)
    {
        try {
            $this->model->where('parent_id', decrypt($id))
                ->orWhere('id', decrypt($id))
                ->update(['deleted_at' => Carbon::now()]);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function keyValue($request)
    {
        $rows = $this->model
            ->whereNULL('deleted_at')
            ->whereHas('translations')
            ->whereStatus(true)
            ->when(empty($request['parentId']) && empty($request['slug']), function ($query) {
                $query->whereNULL('parentId');
            })
            ->when(!empty($request['parentId'] && empty($request['slug'])), function ($query) use ($request) {
                $query->whereParentId(decrypt($request['parentId']));
            })
            ->when(!empty($request['slug'] && empty($request['parentId'])), function ($query) use ($request) {
                $query->whereParentId($this->model->where('slug', $request['slug'])->first()['id']);
            })
            ->get();

        return [
            'rows' => SettingKeyValueResource::collection($rows)
        ];
    }

    public function appSettings($request)
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->whereHas('translations')
            ->whereStatus(true)
            ->first();

        return new SettingMobileResource($row);
    }

    public function isoCountryCodes()
    {
        return [
            'rows' => IsoCountryCodes::getIsoCountryCodes()
        ];
    }
}
