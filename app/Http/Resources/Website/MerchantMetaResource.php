<?php

namespace App\Http\Resources\Website;

class MerchantMetaResource extends JsonResource
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
            'reference_id' => $this->reference_id,
            'gateway' => new PaymentGatewayResource($this->gateway),
            'status' => $this->status,
            'is_able_to_accept_payments' => $this->is_able_to_accept_payments,
            'currently_due' => $this->currently_due,
            'eventually_due' => $this->eventually_due,
            'disabled_reason' => $this->disabled_reason,
            'details_submitted' => $this->details_submitted,
        ];
    }
}
