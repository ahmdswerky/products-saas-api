<?php

namespace App\Http\Resources\Website;

class IntegrationNameResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'url' => $this->url,
            'icon' => $this->icon,
            'created_at' => $this->created_at,
        ];
    }
}
