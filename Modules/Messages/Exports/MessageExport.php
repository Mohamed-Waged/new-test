<?php

namespace Modules\Messages\Exports;

use Helper;
use Modules\Messages\Entities\Message;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MessageExport implements FromCollection, WithHeadings
{
    protected $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Email',
            'Question',
            'Reply',
            'Status',
            'Date'
        ];
    }

    public function collection()
    {
        $request = $this->request;

        $rows = Message::whereNULL('deleted_at')
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
                $query->whereStatus((strtolower($request['status']) == 'active') ? true : false);
            })
            ->latest('id')
            ->paginate($request['paginate'] ?? 10);

        $dataMapping = $rows->transform(function ($row) {
            return [
                'id'            => $row->id,
                'name'          => $row->name,
                'email'         => $row->email,
                'mobile'        => $row->mobile,
                'question'      => $row->question,
                'answer'        => $row->answer,
                'status'        => ($row->is_active == 1)
                    ? 'Replied'
                    : 'New',
                'date'          => date('d-m-Y', strtotime($row->created_at))
            ];
        });

        return collect($dataMapping->toArray());
    }
}
