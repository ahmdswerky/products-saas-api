<?php

namespace App\Http\Controllers\Website;

use App\Http\Filters\Website\IntegrationFilter;
use App\Models\Integration;
use App\Models\IntegrationName;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\IntegrationResource;
use App\Http\Requests\Website\IntegrationStoreRequest;
use App\Http\Requests\Website\IntegrationUpdateRequest;

class IntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IntegrationFilter $filters)
    {
        $integrations = IntegrationName::with(['integration' => function ($query) {
            $query->where('integrations.merchant_id', $this->merchant()->id);
        }])->filter($filters)
            ->orderBy('is_available', 'DESC')
            ->get()
            ->groupBy('category');

        // $integrations = Integration::with(['merchant', 'integrationName'])->filter($filters)->get()->groupBy('integrationName.category');

        return response([
            'data' => $integrations->map(fn ($integration) => IntegrationResource::collection($integration)),
            // 'data' => $integrations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IntegrationStoreRequest $request)
    {
        $data = [
            'key' => $request->key,
            'secret' => $request->secret,
        ];

        Integration::with(['integrationName'])->whereHas('integrationName', function ($query) {
            $query->where('category', 'mail');
        })->where('is_used', true)->first();

        if ($request->is_used) {
            $data['is_used'] = $request->is_used;
        }

        $integration = $this->merchant()->integrations()->updateOrCreate([
            'merchant_id' => $this->merchant()->id,
            'integration_name_id' => $request->integration_name_id,
        ], $data);

        if ($integration->integrationName->category != 'other' && $request->is_used) {
            $siblingIntegrations = $this->merchant()
                ->integrations()
                // ->where('id', '!=', $integration->id)
                ->whereHas('integrationName', function ($query) use ($request) {
                    $query->where('id', '!=', $request->integration_name_id)->where('category', $request->name->category);
                })->get();

            $siblingIntegrations->each->update(['is_used' => false]);
        }

        return response([
            'integration' => $integration,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Integration  $integration
     * @return \Illuminate\Http\Response
     */
    public function show(Integration $integration)
    {
        return response([
            'integration' => new IntegrationResource($integration),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Integration  $integration
     * @return \Illuminate\Http\Response
     */
    public function update(IntegrationUpdateRequest $request, Integration $integration)
    {
        // if ($request->is_used) {
        //     $siblingIntegrations = $this->merchant()
        //         ->integrations()
        //         ->where('id', '!=', $integration->id)
        //         ->whereHas('integrationName', function ($query) use ($request) {
        //             $query->where('category', $request->name->category);
        //         })->get();
        //
        //     $siblingIntegrations->each->update(['is_used' => false]);
        // }
        //
        // $integration->update(
        //     $request->validated()
        // );
        //
        // return response([
        //     'integration' => new IntegrationResource($integration->fresh()),
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Integration  $integration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Integration $integration)
    {
        //
    }
}
