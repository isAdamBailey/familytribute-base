<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObituaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'birth_date' => $this->birth_date,
            'death_date' => $this->death_date,
            'content' => $this->content,
            'background_photo_url' => $this->background_photo_url,
        ];
    }
}
