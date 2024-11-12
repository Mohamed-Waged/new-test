<?php

namespace Modules\Users\Repositories;

use File;
use Excel;
use Helper;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Imageable;
use Illuminate\Support\Str;
use App\Constants\GlobalConstants;
use App\Repositories\BaseRepository;
use Modules\Roles\Entities\Permission;
use Modules\Users\Http\Resources\UserResource;
use Modules\Users\Http\Resources\UserShowResource;
use Modules\Users\Http\Resources\UserKeyValueResource;
use Modules\Users\Repositories\Contracts\UsersRepositoryInterface;

class UsersRepository extends BaseRepository implements UsersRepositoryInterface
{
    protected $model;

    /**
     * @param User $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(User $model)
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
            ->whereNOTIN('id', [1]) // skip root user
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request['search'] . '%')
                        ->orWhere('email', 'like', '%' . $request['search'] . '%')
                        ->orWhere('id', $request['search']);
                });
            })
            ->when(!empty($request['role']), function ($query) use ($request) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('roles.name', $request['role']['name']);
                });
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
            'rows'          => UserResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('users')
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
            $row->name          = $request['name'];
            $row->email         = $request['email'];

            if (!empty($request['password'])) {
                $row->password = bcrypt($request['password']);
            }

            $row->country_code  = $request['countryCode'] ?? NULL;
            $row->mobile        = $request['mobile'] ?? NULL;
            $row->is_active     = Helper::getStatusId($request['status']);
            $row->save();

            $row->modelHasRole()->delete(); // delete old role
            $row->assignRole($request['role']['name'] ?? NULL); // assign new role

            // Imageble
            $row->image()->delete();
            if (!empty($request['imageBase64'])) {
                if (!Str::contains($request['imageBase64'], [Imageable::contains()])) {
                    $image = Imageable::uploadImage($request['imageBase64'], 'users');
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
     * @param mixed $user
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($user): array
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->when(!empty($user), function ($query) use ($user) {
                if (is_numeric($user)) {
                    $query->whereId($user);
                } else {
                    $query->whereId(decrypt($user));
                }
            })
            ->first();

        return [
            'row' => new UserShowResource($row)
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
                // Mark the user as deleted by appending a deleted-at-timestamp to the email
                $destroy->email      .= '-deleted-at-' . Carbon::now()->timestamp;
                $destroy->deleted_by = auth('api')->user()->id;
                $destroy->deleted_at = Carbon::now();
                $destroy->save();
            }

            return true;
        } catch (\Exception $e) {
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
        $fileName   = 'users-' . now()->format('d-m-Y-H-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            if (!File::isDirectory($directory))
                File::makeDirectory(public_path($directory), 0777, true, true);

            Excel::store(new UserExport($request), $path, 'public');

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
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function keyValue($request): array
    {
        $rows = $this->model
            ->whereNULL('deleted_at')
            ->whereStatus(true)
            ->whereNOTIN('id', [1]) // skip root user
            ->when(!empty($request['role']), function ($query) use ($request) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('roles.name', $request['role']['name']);
                });
            })
            ->latest('id')
            ->get();

        return [
            'rows' => UserKeyValueResource::collection($rows)
        ];
    }
}
