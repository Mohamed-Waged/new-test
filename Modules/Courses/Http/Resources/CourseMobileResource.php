<?php
namespace Modules\Courses\Http\Resources;

use Helper;
use App\Models\Imageable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        $fileUrl   = $this->image['url'] ?? NULL;
        $file      = Imageable::getImagePath('course', $fileUrl);

        return [
            'id'            => $this->id,
            'encryptId'     => encrypt($this->id),
            'media' => [
                'type'      => Helper::getFileExtensionType($file),
                'url'       => $file,
            ],
            'title'         => $this->title,
            'body'          => $this->body
        ];
    }
}
