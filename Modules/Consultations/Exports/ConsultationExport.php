<?php

namespace Modules\Consultations\Exports;

use Helper;
use Illuminate\Support\Collection;
use Modules\Consultations\Entities\Consultation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConsultationExport implements FromCollection, WithHeadings
{
    /**
     * @var array $request
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
            'Consultation Type',
            'Status',
            'Date'
        ];
    }

    /**
     * @return Collection
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function collection(): Collection
    {
        $request = $this->request;

        $rows = Consultation::whereNULL('deleted_at')
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request['search'] . '%');
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
                $query->orderBy($key, $value);
            }, function ($else) {
                $else->latest('id');
            })
            ->paginate($request['paginate'] ?? 10);

        $dataMapping = $rows->transform(function ($row) {
            return [
                'id'        => $row->id,
                'name'      => $row->title,
                'status'    => Helper::getStatusKey($row->is_active),
                'date'      => date('d-m-Y', strtotime($row->created_at))
            ];
        });

        return collect($dataMapping->toArray());
    }
}
