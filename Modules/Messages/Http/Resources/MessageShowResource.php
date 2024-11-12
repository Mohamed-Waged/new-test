<?php

namespace Modules\Messages\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageShowResource extends JsonResource
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
            
            'name'              => $this->name,
            'email'             => $this->email,
            'mobile'            => $this->mobile,
            'subject'           => $this->subject,
            'question'          => $this->question,
            'answer'            => $this->answer,
        ];
    }
}