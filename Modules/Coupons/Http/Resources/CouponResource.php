<?php
namespace Modules\Coupons\Http\Resources;

use Str;
use App\Helpers\Helper;
use App\Models\Imageable;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        $imageUrl               = $this->image['url'] ?? NULL;
        $image                  = Imageable::getImagePath('coupons', $imageUrl);

        return [
            'id'                => $this->id,
            'encryptId'         => encrypt($this->id),
            'image'             => $image,

            'lecturerId'       => $this->lecturer_id,
            'couponeableId'    => $this->couponeable_id,
            'couponeableType'  => $this->couponeable_type,
            'couponCount'      => $this->coupon_count,
            'couponPercentage' => $this->coupon_percentage,

            'sort'              => (int)$this->sort,
            'slug'              => $this->slug,

            'status'            => Helper::getStatusKey($this->is_active),
            'date'              => date('d-m-Y', strtotime($this->created_at)),

            'en' => [
                'tinyTitle'     => Str::limit($this->translate('en')['title'], 30, '...'),
                'title'         => $this->translate('en')['title'],
                'body'          => $this->translate('en')['body']
            ],
            'ar' => [
                'tinyTitle'     => Str::limit($this->translate('ar')['title'], 30, '...'),
                'title'         => $this->translate('ar')['title'],
                'body'          => $this->translate('ar')['body']
            ]

        ];
    }
}
