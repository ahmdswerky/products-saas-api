<?php

namespace App\Http\Resources\Website;

class PaymentResource extends JsonResource
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
            'amount' => $this->amount,
            'usd_amount' => $this->usd_amount,
            'status' => $this->status,
            'method' => new PaymentMethodResource($this->method),
            'product' => new ProductSimeplResource($this->product),
            'user' => $this->when($request->query('merchant_id'), new UserResource($this->user)),
            'created_at' => $this->created_at,
        ];
    }
}
