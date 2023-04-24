<?php

namespace App\Http\Resources\Website;

class IntegrationResource extends JsonResource
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
            'id' => optional($this->integration)->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'url' => $this->url,
            'category' => $this->category,
            'integration_name_id' => $this->id,
            'icon' => $this->icon,
            'is_available' => $this->is_available,
            'key' => optional($this->integration)->key,
            'secret' => optional($this->integration)->secret,
            'is_used' => (bool) optional($this->integration)->is_used,
        ];
    }
}
