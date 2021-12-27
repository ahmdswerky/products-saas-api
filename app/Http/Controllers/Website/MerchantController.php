<?php

namespace App\Http\Controllers\Website;

use App\Models\Merchant;
use App\Models\MerchantMeta;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\Website\MerchantFilter;
use App\Http\Resources\MainPaginatedCollection;
use App\Http\Resources\Website\MerchantResource;
use App\Http\Requests\Website\MerchantStoreRequest;
use App\Http\Requests\Website\MerchantUpdateRequest;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MerchantFilter $filters)
    {
        $merchants = Auth::user()->merchants()
            ->filter($filters)
            ->paginate(per_page());

        return new MainPaginatedCollection(
            MerchantResource::collection($merchants)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MerchantStoreRequest $request)
    {
        $method = PaymentService::defaultMethod($request->gateway);

        $payment = PaymentService::init($method);

        if ($request->has('code')) {
            $account = $payment->authorizeMerchant($request->code);
            $accountId = $account->account_id;

            if (!$account->authorized) {
                return response([
                    'message' => 'Unauthorized.',
                ], 401);
            }
        }

        if ($request->has('remote_id')) {
            $accountId = $request->input('remote_id');
        }

        $account = $payment->getMerchant($accountId);
        $merchant = Auth::user()->merchant;

        if (!$merchant) {
            $merchant = Auth::user()->merchants()->create(
                $request->only('title')
            );
        }

        info($account->id);

        $data = [
            'status' => $payment->checkMerchantStatus($account),
            'reference_id' => $account->id,
            'is_able_to_accept_payments' => optional($account)->charges_enabled,
            'currently_due' => optional($account)->currently_due,
            'eventually_due' => optional($account)->eventually_due ?
                json_encode(optional($account)->eventually_due) : null,
            'details_submitted' => optional($account)->details_submitted,
            'currently_due' => optional($account)->currently_due ?
                json_encode(optional($account)->currently_due) : null,
            'eventually_due' => optional($account)->eventually_due ?
                json_encode(optional($account)->eventually_due) : null,
            'disabled_reason' => optional($account)->disabled_reason,
            'primary_email_confirmed' => optional($account)->primary_email_confirmed,
        ];

        MerchantMeta::updateOrCreate(
            $request->only('payment_gateway_id'),
            collect($data)->filter()->toArray(),
        );

        return response([
            'merchant' => new MerchantResource($merchant->refresh()),
        ]);

        //MerchantMeta::updateOrCreate(
        //    [
        //        'payment_gateway_id' => $request->payment_gateway_id,
        //        'mercahnt_id' => $merchant->id,
        //    ],
        //    $request->validated(),
        //);

        //return response([
        //    'merchant' => new MerchantResource($merchant->refresh()),
        //]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function show(Merchant $merchant)
    {
        return response([
            'merchant' => new MerchantResource($merchant->refresh()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function update(MerchantUpdateRequest $request, Merchant $merchant)
    {
        if ($request->has('status') && $request->input('status') === 'disconnected') {
            $meta = MerchantMeta::where('merchant_id', $merchant->id)
                ->where('payment_gateway_id', PaymentGateway::byKey($request->gateway))
                ->first();

            $meta->update($request->only('status'));
        }

        return response([
            'merchant' => new MerchantResource($merchant->refresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Merchant $merchant)
    {
        //
    }
}
