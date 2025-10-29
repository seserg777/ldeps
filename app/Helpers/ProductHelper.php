<?php

namespace App\Helpers;

use App\Models\Product\Product;
use Illuminate\Support\Str;

class ProductHelper
{
    /**
     * Format product price with currency.
     *
     * @param float $price
     * @param string $currency
     * @return string
     */
    public static function formatPrice(float $price, string $currency = 'UAH'): string
    {
        return number_format($price, 2, '.', ' ') . ' ' . $currency;
    }

    /**
     * Generate product slug from name.
     *
     * @param string $name
     * @param string $locale
     * @return string
     */
    public static function generateSlug(string $name, string $locale = 'uk'): string
    {
        return Str::slug($name, '-', $locale);
    }

    /**
     * Check if product is on sale.
     *
     * @param Product $product
     * @return bool
     */
    public static function isOnSale(Product $product): bool
    {
        return $product->product_old_price > 0 && $product->product_old_price > $product->product_price;
    }

    /**
     * Calculate discount percentage.
     *
     * @param Product $product
     * @return float
     */
    public static function getDiscountPercentage(Product $product): float
    {
        if (!self::isOnSale($product)) {
            return 0;
        }

        return round((($product->product_old_price - $product->product_price) / $product->product_old_price) * 100, 2);
    }

    /**
     * Get product availability status.
     *
     * @param Product $product
     * @return string
     */
    public static function getAvailabilityStatus(Product $product): string
    {
        if ($product->product_quantity > 0) {
            return 'in_stock';
        }

        if ($product->product_quantity === 0) {
            return 'out_of_stock';
        }

        return 'pre_order';
    }

    /**
     * Get product availability text.
     *
     * @param Product $product
     * @return string
     */
    public static function getAvailabilityText(Product $product): string
    {
        return match (self::getAvailabilityStatus($product)) {
            'in_stock' => 'В наличии',
            'out_of_stock' => 'Нет в наличии',
            'pre_order' => 'Под заказ',
            default => 'Неизвестно'
        };
    }

    /**
     * Generate product meta description.
     *
     * @param Product $product
     * @return string
     */
    public static function generateMetaDescription(Product $product): string
    {
        $description = $product->short_description ?: $product->name;
        $description = strip_tags($description);
        $description = Str::limit($description, 160);

        return $description;
    }

    /**
     * Get product rating stars.
     *
     * @param Product $product
     * @return array
     */
    public static function getRatingStars(Product $product): array
    {
        $rating = $product->product_rating ?? 0;
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);

        return [
            'rating' => $rating,
            'full_stars' => $fullStars,
            'has_half_star' => $hasHalfStar,
            'empty_stars' => $emptyStars,
        ];
    }
}
