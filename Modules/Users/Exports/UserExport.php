<?php

namespace Modules\Users\Exports;

use Helper;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserExport implements FromCollection, WithHeadings
{
    /**
     * @var array
     */
    protected $request;

    /**
     * @param array $request
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
   public function headings(): array
   {
        return [
            'Id',
            'Name',
            'Email',
            'Mobile',
            'Role',
            'Status',
            'Date'
       ];
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function collection(): Collection
    {
        $request = $this->request;

        $rows = User::
                        whereNULL('deleted_at')
                        ->whereNOTIN('id', [1]) // skip root user
                        ->when(!empty($request['search']), function($query) use ($request) {
                            $query->where(function($q) use ($request) {
                                $q->where('name', 'like','%'.$request['search'].'%')
                                    ->orWhere('email', 'like', '%'.$request['search'].'%')
                                    ->orWhere('id', $request['search']);
                            });
                        })
                        ->when(!empty($request['role']), function($query) use ($request) {
                            $query->whereHas('roles', function($q) use ($request) {
                                $q->where('roles.name', $request['role']['name']);
                            });
                        })
                        ->when(!empty($request['date']), function($query) use ($request) {
                            $query->whereDate('created_at', $request['date']);
                        })
                        ->when(!empty($request['status']), function($query) use ($request) {
                            $query->whereStatus((strtolower($request['status']) == 'active') ? true : false);
                        })
                        ->when(!empty($request['sortBy']), function($query) use ($request) {
                            $key   = $request['sortBy'][0]['key'];
                            $value = $request['sortBy'][0]['order'];
                            if ($key == 'title') {
                                $query->orderByTranslation('title', $value);
                            } else {
                                $query->orderBy($key, $value);
                            }
                        }, function($else) {
                            $else->latest('id');
                        })
                        ->paginate($request['paginate'] ?? 10);

        $dataMapping = $rows->transform(function($row) {
            return [
                'id'            => $row->id,
                'name'          => $row->name,
                'email'         => $row->email,
                'mobile'        => $row->mobile,
                'role'          => $row->roles()->first()->name ?? '',
                'status'        => Helper::getStatusKey($row->is_active),
                'date'          => date('d-m-Y', strtotime($row->created_at))
            ];
        });

        return collect($dataMapping->toArray());
    }
}
