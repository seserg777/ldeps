<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create categories first
        $categories = [
            [
                'name' => 'Оптичні кабелі',
                'slug' => 'optical-cables',
                'description' => 'Високоякісні оптичні кабелі для професійного використання',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Мережеве обладнання',
                'slug' => 'network-equipment',
                'description' => 'Обладнання для побудови мережевої інфраструктури',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Кабелі та провідники',
                'slug' => 'cables-conductors',
                'description' => 'Різноманітні кабелі та провідники',
                'sort_order' => 3,
                'is_active' => true
            ]
        ];

        foreach ($categories as $categoryData) {
            \App\Models\Category\Category::create($categoryData);
        }

        // Create products
        $products = [
            [
                'name' => 'Оптичний кабель FinMark LTxxx-SM-05',
                'description' => 'Високоякісний оптичний кабель для професійного використання',
                'price' => 1500.00,
                'image' => 'cable1.jpg',
                'manufacturer_id' => 1,
                'hits' => 150,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark UTxxx-SM-03-T',
                'description' => 'Оптичний кабель з підвищеною міцністю',
                'price' => 2200.00,
                'image' => 'cable2.jpg',
                'manufacturer_id' => 1,
                'hits' => 200,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark FTTHxxx-SM-18/Flex',
                'description' => 'Гнучкий оптичний кабель для FTTH мереж',
                'price' => 1800.00,
                'image' => 'cable3.jpg',
                'manufacturer_id' => 1,
                'hits' => 120,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark UTxxx-SM-15',
                'description' => 'Популярний оптичний кабель середнього класу',
                'price' => 3200.00,
                'image' => 'cable4.jpg',
                'manufacturer_id' => 1,
                'hits' => 300,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark LTxxx-SM-12',
                'description' => 'Надійний оптичний кабель для довгих відстаней',
                'price' => 2800.00,
                'image' => 'cable5.jpg',
                'manufacturer_id' => 1,
                'hits' => 180,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark FTTHxxx-SM-24/Flex',
                'description' => 'Гнучкий кабель для FTTH з 24 волокнами',
                'price' => 2500.00,
                'image' => 'cable6.jpg',
                'manufacturer_id' => 1,
                'hits' => 90,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark UTxxx-SM-08',
                'description' => 'Компактний оптичний кабель для міських мереж',
                'price' => 1900.00,
                'image' => 'cable7.jpg',
                'manufacturer_id' => 1,
                'hits' => 160,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark LTxxx-SM-20',
                'description' => 'Преміум оптичний кабель для критично важливих мереж',
                'price' => 4500.00,
                'image' => 'cable8.jpg',
                'manufacturer_id' => 1,
                'hits' => 75,
                'status' => true
            ],
            [
                'name' => 'Оптичний кабель FinMark FTTHxxx-SM-36/Flex',
                'description' => 'Високопродуктивний кабель з 36 волокнами',
                'price' => 3800.00,
                'image' => 'cable9.jpg',
                'manufacturer_id' => 1,
                'hits' => 110,
                'status' => true
            ]
        ];

        foreach ($products as $productData) {
            \App\Models\Product\Product::create($productData);
        }
    }
}