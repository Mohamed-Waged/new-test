<?php
namespace Modules\Consultations\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'encryptId'         => encrypt($this->id),

            //
        ];
    }
}
