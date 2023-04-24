<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MainPaginatedCollection extends ResourceCollection
{
    private $resourceData;

    public function __construct($resource)
    {
        $this->resourceData = [
            'data' => [],
            'total' => $resource->total(),
            //'count' => $resource->count(),
            'per_page' => (int) $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'last_page' => $resource->lastPage(),
            'has_more' => $resource->lastPage() > $resource->currentPage(),
        ];

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }

    public function toArray($request)
    {
        $this->resourceData['data'] = $this->collection;

        return $this->resourceData;
    }
}
