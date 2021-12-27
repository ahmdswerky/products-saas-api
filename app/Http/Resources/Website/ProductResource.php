<?php

namespace App\Http\Resources\Website;

class ProductResource extends JsonResource
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
            'public_id' => $this->public_id,
            'title' => $this->title,
            'description' => $this->description,
            'total_payments' => $this->toalPayments,
            'slug' => $this->slug,
            'category' => $this->category,
            'photo' => new MediaResource($this->photo),
            'usd_price' => $this->usd_price,
            'price' => $this->price,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
            'merchant_ids' => $this->merchantIds,
            'merchant' => new MerchantResource($this->merchant),
            'created_at' => $this->created_at,
        ];
    }
}
