<?php

namespace Modules\Books\Exports;

use Helper;
use App\Constants\GlobalConstants;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Books\Repositories\BooksRepository;

class BookExport implements FromCollection, WithHeadings
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
            'Book Type',
            'Book Name',
            'Price',
            'Pages No.',
            'Published At',
            'Status',
            'Issued Date'
        ];
    }

    /**
     * @return Collection
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function collection(): Collection
    {
        $request = $this->request;

        $bookRepoistory = app(BooksRepository::class);

        $rows = $bookRepoistory->buildQuery($request)->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        $dataMapping = $rows->transform(function ($row) {
            return [
                'id'            => $row->id,
                'book_type'     => optional($row->bookType)->title,
                'name'          => $row->title,
                'price'         => $row->price,
                'pages_no'      => $row->pages_no,
                'published_at'  => Helper::formatDate($row->published_at),
                'status'        => Helper::getStatusKey($row->is_active),
                'issued_date'   => Helper::formatDate($row->created_at)
            ];
        });

        return collect($dataMapping->toArray());
    }
}
