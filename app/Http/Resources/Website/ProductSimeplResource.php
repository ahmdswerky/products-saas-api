<?php

namespace App\Http\Resources\Website;

class ProductSimeplResource extends JsonResource
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
            'id' => $this->id(),
            'title' => $this->title,
            'slug' => $this->slug,
            'photo' => new MediaResource($this->photo),
            'usd_price' => $this->usd_price,
            'price' => $this->price,
            'currency' => $this->currency,
        ];
    }
}
