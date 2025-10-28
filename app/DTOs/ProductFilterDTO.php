<?php

namespace App\DTOs;

class ProductFilterDTO
{
    public function __construct(
        public readonly ?float $priceMin = null,
        public readonly ?float $priceMax = null,
        public readonly array $manufacturers = [],
        public readonly ?string $category = null,
        public readonly string $sort = 'newest',
        public readonly int $perPage = 12,
        public readonly int $page = 1
    ) {}

    /**
     * Create from request data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            priceMin: $data['price_min'] ?? null,
            priceMax: $data['price_max'] ?? null,
            manufacturers: $data['manufacturer'] ?? [],
            category: $data['category'] ?? null,
            sort: $data['sort'] ?? 'newest',
            perPage: $data['per_page'] ?? 12,
            page: $data['page'] ?? 1
        );
    }

    /**
     * Get manufacturer IDs as array.
     *
     * @return array
     */
    public function getManufacturerIds(): array
    {
        return is_array($this->manufacturers) ? $this->manufacturers : [$this->manufacturers];
    }

    /**
     * Check if price filter is active.
     *
     * @return bool
     */
    public function hasPriceFilter(): bool
    {
        return $this->priceMin !== null || $this->priceMax !== null;
    }

    /**
     * Check if manufacturer filter is active.
     *
     * @return bool
     */
    public function hasManufacturerFilter(): bool
    {
        return !empty($this->manufacturers);
    }
}
