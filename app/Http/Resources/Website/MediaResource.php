<?php

namespace App\Http\Resources\Website;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'path' => $this->displayedPath,
            'type' => $this->type,
            'name' => $this->name,
            //'title' => $this->title,
            //'description' => $this->description,
            //'notes' => $this->notes,
            'is_main' => $this->is_main,
        ];
    }
}
