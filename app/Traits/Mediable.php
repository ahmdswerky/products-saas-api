<?php

namespace App\Traits;

use ReflectionClass;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait Mediable
{
    public $mediableDriver = 'local';

    public function media($type = null, ?string $name = null)
    {
        return $this->morphMany(Media::class, 'mediable')
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($name, function ($query) use ($name) {
                $query->where('name', $name);
            });
    }

    public function mainMedia()
    {
        return $this->media()->where('is_main', true);
    }

    public function getMainMediaAttribute()
    {
        return $this->mainMedia()->first();
    }

    public function otherMedia()
    {
        return $this->media()->where('is_main', false);
    }

    public function photo(?string $name = null)
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('type', 'photo')
            ->when($name, function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->select([
                'id',
                'type',
                'mediable_type',
                'mediable_id',
                'path',
                'name',
                'title',
                'description',
                'is_main',
            ]);
    }

    public function addMedia($path, $type = 'photo', bool $main = false, array $additional = [], bool $local = false)
    {
        if ($path instanceof UploadedFile) {
            $path = $this->storeFile($path, $type);
        }

        if ($local) {
            $path = $this->storeFileFromUrl($path, $type);
        }

        if (isset($additional['path'])) {
            $path = $additional['path'];
        }

        return $this->media()->create([
            'path' => $path,
            'type' => $type,
            'driver' => $this->mediableDriver,
            'name' => $additional['name'] ?? null,
            'title' => $additional['title'] ?? null,
            'group' => $additional['group'] ?? null,
            'description' => $additional['description'] ?? null,
            'notes' => $additional['notes'] ?? null,
            'is_main' => $main,
            //'user_id' => auth()->check() ? auth()->id() : null,
        ], $this);
    }

    public function addMediaUrl($url, $type = 'photo', $main = false, array $additional = [])
    {
        return $this->media()->create([
            'path' => $url,
            'type' => $type,
            'driver' => 'network',
            'name' => $additional['name'] ?? null,
            'title' => $additional['title'] ?? null,
            'group' => $additional['group'] ?? null,
            'description' => $additional['description'] ?? null,
            'notes' => $additional['notes'] ?? null,
            'is_main' => (bool) $main,
            //'user_id' => auth()->check() ? auth()->id() : null,
        ], $this);
    }

    public function updateMedia(Media $media, $path, array $additional = [])
    {
        if ($path instanceof UploadedFile) {
            $path = $this->storeFile($path, $media->type);
        }

        $this->deleteOldMedia($media);

        $media->update([
            'path' => $path,
            'driver' => $this->mediableDriver,
            'name' => $additional['name'] ?? $media->name,
            'title' => $additional['title'] ?? $media->title,
            'description' => $additional['description'] ?? $media->description,
            'notes' => $additional['notes'] ?? $media->notes,
            'is_main' => $additional['is_main'] ?? $media->is_main,
        ]);
    }

    public function addOrUpdateMedia(Media $media, string $path, string $type, bool $main, array $additional = [])
    {
        if (!$media) {
            return $this->addMedia($path, $type, $main, $additional);
        }

        if ($path instanceof UploadedFile) {
            $path = $this->storeFile($path, $media->type);
        }

        $this->deleteOldMedia($media);

        // TODO: call method from here
        $media->update([
            'path' => $path,
            'driver' => $this->mediableDriver,
            'name' => get_value($additional, 'name', $media->name),
            'title' => get_value($additional, 'title', $media->title),
            'description' => get_value($additional, 'description', $media->description),
            'notes' => get_value($additional, 'notes', $media->notes),
            'is_main' => get_value($additional, 'is_main', $media->is_main),
        ]);
    }

    public function deleteOldMedia(Media $media)
    {
        if (Str::contains($media->path, url(''))) {
            if (explode('storage', $media->path) && count(explode('storage', $media->path)) > 0) {
                if (count(explode('storage', $media->path)) > 1) {
                    $path = explode('storage', $media->path)[1];
                    $path = 'public' . $path;
                    @Storage::delete($path);
                }
            }
        }
    }

    public function mediableDriver($driver)
    {
        $this->mediableDriver = $driver;

        return $this;
    }

    protected function storeFile(UploadedFile $requestFile, string $type)
    {
        $type   = Str::plural(strtolower($type));
        $typeDirectory = $type . "DirectoryMedia";
        $path = config("media.{$type}.path") . $this->$typeDirectory;
        $extention = '.' . $requestFile->getClientOriginalExtension();
        $filename = Str::random(config('media.filename.length')) . $extention;

        // TODO: finish
        //return Storage::disk($this->mediableDriver)->putFileAs($path, $requestFile, $filename);
        return Storage::disk($this->mediableDriver)->put($path . '/' . $filename, $requestFile);
    }

    protected function storeFileFromUrl(string $url, string $type)
    {
        $type   = Str::plural(strtolower($type));
        $typeDirectory = $type . "DirectoryMedia";
        $content = file_get_contents($url);
        $filename = config("media.{$type}.path") .
            $this->$typeDirectory .
            '/' .
            Str::random(config('media.filename.length')) .
            '.' .
            'png';

        Storage::disk($this->mediableDriver)->put($filename, $content);

        return $filename;
    }

    public function getPhotosDirectoryMediaAttribute()
    {
        $class = new ReflectionClass($this);
        return Str::plural(strtolower($class->getShortName()));
    }

    //? ==== Delete ==== //
    public function deleteMedia(Media $media)
    {
        @Storage::delete($media->path);
        $media->delete();
    }
}
