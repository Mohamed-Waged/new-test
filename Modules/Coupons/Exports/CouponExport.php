<?php

namespace Modules\Coupons\Exports;

use App\Helpers\Helper;
use App\Constants\GlobalConstants;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Coupons\Repositories\CouponsRepository;

class CouponExport implements FromCollection, WithHeadings
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
            'Coupon Name',
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

        $bookRepoistory = app(CouponsRepository::class);

        $rows = $bookRepoistory->buildQuery($request)->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        $dataMapping = $rows->transform(function ($row) {
            return [
                'id'            => $row->id,
                'name'          => $row->title,
                'status'        => Helper::getStatusKey($row->is_active),
                'issued_date'   => Helper::formatDate($row->created_at)
            ];
        });

        return collect($dataMapping->toArray());
    }
}
