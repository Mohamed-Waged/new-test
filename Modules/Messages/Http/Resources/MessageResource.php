<?php

namespace Modules\Messages\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'encryptId'         => encrypt($this->id),

            'nursery' => [
                'id'            => $this->nursery_id,
                'name'          => $this->nursery?->title ?? ''
            ],

            'branch' => [
                'id'            => $this->branch_id,
                'name'          => $this->branch?->title ?? ''
            ],

            'name'              => $this->name,
            'email'             => $this->email,
            'mobile'            => $this->mobile,
            'question'          => $this->question,
            'answer'            => $this->answer,
            'date'              => date('d-m-Y', strtotime($this->created_at)),
            'status'            => ($this->is_active == 1)
                                    ? 'Replied'
                                    : 'New',
        ];
    }
}
