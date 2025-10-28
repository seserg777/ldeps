<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manufacturer;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manufacturers = [
            [
                'manufacturer_id' => 1,
                'code_1c' => 'FIN001',
                'manufacturer_url' => 'https://finmark.com.ua',
                'manufacturer_logo' => 'finmark-logo.png',
                'manufacturer_publish' => 1,
                'products_page' => 20,
                'products_row' => 4,
                'ordering' => 1,
                'name_en-GB' => 'FinMark',
                'alias_en-GB' => 'finmark',
                'short_description_en-GB' => 'Professional optical cable manufacturer',
                'description_en-GB' => 'FinMark is a leading manufacturer of high-quality optical cables and fiber optic solutions for professional networks.',
                'meta_title_en-GB' => 'FinMark - Professional Optical Cables',
                'meta_description_en-GB' => 'High-quality optical cables and fiber optic solutions from FinMark',
                'meta_keyword_en-GB' => 'optical cables, fiber optic, FinMark',
                'name_uk-UA' => 'FinMark',
                'alias_uk-UA' => 'finmark',
                'short_description_uk-UA' => 'Професійний виробник оптичних кабелів',
                'description_uk-UA' => 'FinMark - провідний виробник високоякісних оптичних кабелів та волоконно-оптичних рішень для професійних мереж.',
                'meta_title_uk-UA' => 'FinMark - Професійні оптичні кабелі',
                'meta_description_uk-UA' => 'Високоякісні оптичні кабелі та волоконно-оптичні рішення від FinMark',
                'meta_keyword_uk-UA' => 'оптичні кабелі, волоконна оптика, FinMark',
                'manufacturer_status' => 'active'
            ],
            [
                'manufacturer_id' => 2,
                'code_1c' => 'COR002',
                'manufacturer_url' => 'https://corning.com',
                'manufacturer_logo' => 'corning-logo.png',
                'manufacturer_publish' => 1,
                'products_page' => 20,
                'products_row' => 4,
                'ordering' => 2,
                'name_en-GB' => 'Corning',
                'alias_en-GB' => 'corning',
                'short_description_en-GB' => 'Global leader in optical communications',
                'description_en-GB' => 'Corning is a global leader in optical communications, providing innovative solutions for telecommunications networks.',
                'meta_title_en-GB' => 'Corning - Optical Communications Solutions',
                'meta_description_en-GB' => 'Innovative optical communications solutions from Corning',
                'meta_keyword_en-GB' => 'optical communications, Corning, telecommunications',
                'name_uk-UA' => 'Corning',
                'alias_uk-UA' => 'corning',
                'short_description_uk-UA' => 'Світовий лідер в оптичних комунікаціях',
                'description_uk-UA' => 'Corning - світовий лідер в галузі оптичних комунікацій, що надає інноваційні рішення для телекомунікаційних мереж.',
                'meta_title_uk-UA' => 'Corning - Рішення оптичних комунікацій',
                'meta_description_uk-UA' => 'Інноваційні рішення оптичних комунікацій від Corning',
                'meta_keyword_uk-UA' => 'оптичні комунікації, Corning, телекомунікації',
                'manufacturer_status' => 'active'
            ],
            [
                'manufacturer_id' => 3,
                'code_1c' => 'PRI003',
                'manufacturer_url' => 'https://prysmian.com',
                'manufacturer_logo' => 'prysmian-logo.png',
                'manufacturer_publish' => 1,
                'products_page' => 20,
                'products_row' => 4,
                'ordering' => 3,
                'name_en-GB' => 'Prysmian',
                'alias_en-GB' => 'prysmian',
                'short_description_en-GB' => 'Leading cable manufacturer',
                'description_en-GB' => 'Prysmian is a world leader in the energy and telecom cable systems industry.',
                'meta_title_en-GB' => 'Prysmian - Cable Solutions',
                'meta_description_en-GB' => 'Leading cable manufacturer for energy and telecom systems',
                'meta_keyword_en-GB' => 'cables, Prysmian, energy, telecom',
                'name_uk-UA' => 'Prysmian',
                'alias_uk-UA' => 'prysmian',
                'short_description_uk-UA' => 'Провідний виробник кабелів',
                'description_uk-UA' => 'Prysmian - світовий лідер в галузі енергетичних та телекомунікаційних кабельних систем.',
                'meta_title_uk-UA' => 'Prysmian - Кабельні рішення',
                'meta_description_uk-UA' => 'Провідний виробник кабелів для енергетичних та телекомунікаційних систем',
                'meta_keyword_uk-UA' => 'кабелі, Prysmian, енергетика, телекомунікації',
                'manufacturer_status' => 'active'
            ]
        ];

        foreach ($manufacturers as $manufacturerData) {
            Manufacturer::create($manufacturerData);
        }
    }
}
