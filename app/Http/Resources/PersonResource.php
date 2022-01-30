<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name'=> $this->last_name,
            'photo_url' => $this->photo_url,
            'parent_ids' => $this->parent_ids,
            'obituary' => ObituaryResource::make($this->whenLoaded('obituary')),
            'pictures' => PictureResource::collection($this->whenLoaded('pictures')),
            'stories' => StoryResource::collection($this->whenLoaded('stories')),
            'parents' => $this->whenLoaded('parents'),
            'children' => $this->whenLoaded('children'),
        ];
    }
}