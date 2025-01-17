<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'email' => $this->user->name,
            'long_url' => $this->long_url,
            'short_url' => $this->short_url
        ];
    }
}
