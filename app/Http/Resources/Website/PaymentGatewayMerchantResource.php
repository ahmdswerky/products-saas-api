<?php

namespace App\Http\Resources\Website;

class PaymentGatewayMerchantResource extends JsonResource
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
            'gateway' => optional($this->gateway)->key,
            'status' => $this->status,
        ];
    }
}
