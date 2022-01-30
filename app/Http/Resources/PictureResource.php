<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PictureResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'year' => $this->year,
            'url' => $this->url,
            'featured' => $this->featured,
            'private' => $this->private,
            'description' => $this->description,
            'person_ids' => $this->person_ids,
            'people' => PersonResource::collection($this->whenLoaded('people')),
        ];
    }
}
