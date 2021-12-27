<?php

namespace App\Http\Resources\Website;
use Illuminate\Support\Facades\Auth;

class PaymentGatewayResource extends JsonResource
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
            'key' => $this->key,
            'name' => $this->name,
            //'merchants' => $this->when(
            //    Auth::check(),
            //    PaymentGatewayMerchantResource::collection(Auth::user()->merchants),
            //),
        ];
    }
}
