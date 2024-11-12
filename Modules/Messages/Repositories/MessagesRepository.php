<?php

namespace Modules\Messages\Repositories;

use File;
use Excel;
use Helper;
use Carbon\Carbon;
use App\Constants\GlobalConstants;
use App\Repositories\BaseRepository;
use Modules\Messages\Entities\Message;
use Modules\Roles\Entities\Permission;
use Modules\Messages\Exports\MessageExport;
use Modules\Messages\Http\Resources\MessageResource;
use Modules\Messages\Http\Resources\MessageShowResource;
use Modules\Messages\Repositories\Contracts\MessagesRepositoryInterface;

class MessagesRepository extends BaseRepository implements MessagesRepositoryInterface
{
    protected $model;

    public function __construct(Message $model)
    {
        $this->model = $model;
    }

    public function index($request)
    {
        $rows = $this->model
            ->whereNULL('deleted_at')
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request['search'] . '%')
                        ->orWhere('email', 'like', '%' . $request['search'] . '%')
                        ->orWhere('id', $request['search']);
                });
            })
            ->when(!empty($request['date']), function ($query) use ($request) {
                $query->whereDate('created_at', $request['date']);
            })
            ->when(!empty($request['status']), function ($query) use ($request) {
                $query->whereIsRead((strtolower($request['status']) == 'active') ? true : false);
            })
            ->latest('id')
            ->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        return [
            'rows'          => MessageResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('messages')
        ];
    }


    public function createOrUpdate($request, $id = NULL)
    {
        try {

            if (!empty($id)) {
                $row                = $this->model::find(decrypt($id));
                $row->reply         = $request['reply'] ?? NULL;
                $row->is_read       = true;
                $row->read_at       = Carbon::now();
                $row->save();
            } else {
                $row                = new $this->model;
                $row->name          = $request['name'];
                $row->email         = $request['email'];
                $row->question      = $request['question'];
                $row->is_read       = false;
                $row->save();
            }

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function find($id)
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->where('id', decrypt($id))
            ->first();

        return [
            'row' => new MessageShowResource($row)
        ];
    }

    public function destroy($id)
    {
        try {
            $this->model->where('id', decrypt($id))->update([
                'deleted_by'    => auth('api')->user()->id,
                'deleted_at'    => Carbon::now()
            ]);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function export($request)
    {
        $fileName   = 'messages-' . date('d-m-Y-h-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            if (!File::isDirectory($directory))
                File::makeDirectory(public_path($directory), 0777, true, true);

            Excel::store(new MessageExport($request), $path, 'public');

            return [
                'status'    => true,
                'path'      => request()->root() . '/' . $path,
            ];
        } catch (\Exception $e) {
            return [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        }
    }
}
