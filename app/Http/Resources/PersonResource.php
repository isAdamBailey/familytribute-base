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
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        return [
            'slug' => $this->slug,
            'first_name' => $this->first_name,
            'full_name' => $this->full_name,
            'photo_url' => $this->photo_url,
            'obituary' => ObituaryResource::make($this->whenLoaded('obituary')),
            'pictures' => PictureResource::collection($this->whenLoaded('pictures')),
            'stories' => StoryResource::collection($this->whenLoaded('stories')),
            'parents' => ParentChildResource::collection($this->whenLoaded('parents')),
            'children' => ParentChildResource::collection($this->whenLoaded('children')),
            $this->mergeWhen(auth()->check(), [
                'parent_ids' => $this->parent_ids,
                'last_name' => $this->last_name,
            ]),
        ];
    }
}
