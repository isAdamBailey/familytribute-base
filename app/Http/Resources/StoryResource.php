<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
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
            'content' => $this->content,
            'year' => $this->year,
            'excerpt' => $this->excerpt,
            'people' => PersonResource::collection($this->whenLoaded('people')),
            $this->mergeWhen(auth()->check(), [
                'person_ids' => $this->person_ids,
                'private' => $this->private,
            ]),
        ];
    }
}
