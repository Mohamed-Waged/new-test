<?php
namespace Modules\Reports\Http\Resources;

use Helper;
use App\Models\Imageable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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



        ];
    }
}
