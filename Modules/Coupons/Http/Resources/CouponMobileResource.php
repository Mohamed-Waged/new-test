<?php
namespace Modules\Coupons\Http\Resources;

use App\Helpers\Helper;
use App\Models\Imageable;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        $imageUrl           = $this->image['url'] ?? NULL;
        $image              = Imageable::getImagePath('coupons', $imageUrl);
        
        return [
            'id'            => $this->id,
            'encryptId'     => encrypt($this->id),

            'media' => [
                'type'      => Helper::getFileExtensionType($image),
                'url'       => $image,
            ],

            'title'         => $this->title,
            'body'          => $this->body
        ];
    }
}
