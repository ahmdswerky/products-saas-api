<?php

namespace App\Traits;

use App\Models\Reference;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Referenceable
{
    public function references($type = null, $reference_id = null): MorphMany
    {
        return $this->morphMany(Reference::class, 'referencable')
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($reference_id, function ($query) use ($reference_id) {
                $query->where('reference_id', $reference_id);
            });
    }

    public function scopeReferenceById($query, $type, $id)
    {
        return $query->whereHas('references', function ($query) use ($type, $id) {
            $query->where('type', $type)
                ->where('reference_id', $id);
        });
    }

    public static function byReference($type, $reference_id = null)
    {
        return optional(Reference::byReference($type, $reference_id)->first())->referencable;
    }

    public function getReferenceIdByType($type): ?string
    {
        return optional($this->references($type)->first())->reference_id;
    }

    public function createMultipleReferences(array $references)
    {
        $result = collect([]);

        info(implode(',', collect($references)->keys()->toArray()));


        collect($references)->keys()->map(function ($key) use ($references, &$result) {
            $reference = $this->firstOrCreateReference($key, $references[$key]);

            $result->put($key, $reference);
        });

        return $result;
    }

    public function firstOrCreateReference(string $type, string $reference_id): Reference
    {
        if ($reference = $this->references($type, $reference_id)->first()) {
            return $reference;
        }

        return $this->createReference($type, $reference_id);
    }

    public function createReference(string $type, string $reference_id): Reference
    {
        return $this->references()->create([
            'type' => $type,
            'reference_id' => $reference_id,
        ]);
    }
}
