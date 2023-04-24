<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainPaginatedCollection;
use App\Http\Resources\Website\IntegrationNameResource;
use App\Models\IntegrationName;

class IntegrationNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $integrations = IntegrationName::query()->paginate(per_page());

        return new MainPaginatedCollection(
            IntegrationNameResource::collection($integrations)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\IntegrationName  $integrationName
     * @return \Illuminate\Http\Response
     */
    public function show(IntegrationName $integrationName)
    {
        return response([
            'integration' => new IntegrationNameResource($integrationName),
        ]);
    }
}
