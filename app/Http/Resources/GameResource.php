<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'uploadTimestamp' => optional($this->versions()->latest()->first())
                ->created_at?->toISOString(),
            'author' => optional($this->author)->username,
            'scoreCount' => $this->versions?->sum(function ($version) {
                return $version->scores?->count() ?? 0;
            }) ?? 0,
            'storagePath' => $this->versions()->latest()->first()?->storage_path,
            'versions' => $this->versions->map(function ($version) {
                return [
                    'version' => $version->version,
                    'created_at' => $version->created_at->toDateTimeString(),
                ];
            }),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
