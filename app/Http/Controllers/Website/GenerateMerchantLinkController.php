<?php

namespace App\Http\Controllers\Website;

use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Website\MerchantLinkRequest;

class GenerateMerchantLinkController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(MerchantLinkRequest $request)
    {
        $merchant = Auth::user()->merchant;
        $method = PaymentService::defaultMethod($request->gateway);

        $link = PaymentService::init($method)->createMerchantLink(
            $merchant->email,
            $merchant->userName,
            client_url('connect/' . $request->gateway),
            $merchant->title,
        );

        return response([
            'link' => $link->url,
            //'refresh_url' => $link->refresh_url,
            //'return_url' => $link->return_url,
        ]);
    }
}
