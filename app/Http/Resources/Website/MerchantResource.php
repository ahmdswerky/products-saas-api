<?php

namespace App\Http\Resources\Website;

class MerchantResource extends JsonResource
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
            'api_key' => $this->api_key,
            'title' => $this->title,
            'reference_id' => $this->reference_id,
            'metas' => MerchantMetaResource::collection($this->metas),
            'status' => $this->status,
        ];
    }
}
