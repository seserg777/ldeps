<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->product_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'price' => $this->formatted_price,
            'original_price' => $this->product_price,
            'image' => $this->thumbnail_url,
            'description' => $this->short_description,
            'manufacturer' => [
                'id' => $this->manufacturer?->manufacturer_id,
                'name' => $this->manufacturer?->name,
            ],
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'ean' => $this->product_ean,
            'manufacturer_code' => $this->manufacturer_code,
            'url' => route('products.show-by-path', $this->full_path),
            'is_published' => $this->product_publish,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
