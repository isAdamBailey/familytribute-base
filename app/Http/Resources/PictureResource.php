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
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'year' => $this->year,
            'url' => $this->url,
            'description' => $this->description,
            'people' => PersonResource::collection($this->whenLoaded('people')),
            $this->mergeWhen(auth()->check(), [
                'person_ids' => $this->person_ids,
                'featured' => $this->featured,
                'private' => $this->private,
            ]),
        ];
    }
}
