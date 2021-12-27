<?php

namespace App\Http\Resources\Website;

class UserResource extends JsonResource
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
            'name' => $this->name ?? 'user',
            'email' => $this->email,
            'avatar' => new MediaResource($this->avatar),
            'merchant' => new MerchantResource($this->merchant),
            'created_at' => $this->created_at,
        ];
    }
}
