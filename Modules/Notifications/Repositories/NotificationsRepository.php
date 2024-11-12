<?php

namespace Modules\Notifications\Repositories;

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
use Modules\Notifications\Entities\Notification;
use Modules\Notifications\Http\Resources\NotificationResource;
use Modules\Notifications\Http\Resources\NotificationShowMobileResource;
use Modules\Notifications\Repositories\Contracts\NotificationsRepositoryInterface;

class NotificationsRepository extends BaseRepository implements NotificationsRepositoryInterface
{
    protected $model;

    /**
     * @param Notification $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Notification $model)
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
            'rows'          => NotificationResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('notifications')
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
}
