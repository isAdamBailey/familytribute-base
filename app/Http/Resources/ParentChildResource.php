<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentChildResource extends JsonResource
{
    public $resource = 'person';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'slug' => $this->slug,
            'photo_url' => $this->photo_url,
            'full_name' => $this->full_name,
        ];
    }
}
