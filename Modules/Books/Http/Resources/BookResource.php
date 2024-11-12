<?php
namespace Modules\Books\Http\Resources;

use Helper;
use App\Models\Imageable;
use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        $imageUrl   = $this->image['url'] ?? NULL;
        $image      = Imageable::getImagePath('books', $imageUrl);

        return [
            'id'                => $this->id,
            'encryptId'         => encrypt($this->id),
            'image'             => $image,

            'title'             => $this->title,
            'sort'              => (int)$this->sort,

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
