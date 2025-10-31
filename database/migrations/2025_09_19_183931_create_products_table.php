<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->integer('product_id')->primary();
            $table->string('product_unit_quantity', 100);
            $table->tinyInteger('product_is_veto');
            $table->integer('product_is_software')->default(0);
            $table->integer('product_qsign')->default(0);
            $table->string('product_special_icons', 500);
            $table->string('product_ean', 32)->comment('167387+id');
            $table->string('product_ean_copy', 32);
            $table->string('manufacturer_code', 100);
            $table->decimal('product_quantity', 12, 2);
            $table->integer('product_unit')->default(3);
            $table->boolean('unlimited');
            $table->string('product_availability', 128);
            $table->datetime('product_date_added')->nullable();
            $table->datetime('date_modify')->nullable();
            $table->boolean('product_publish')->default(0);
            $table->boolean('installment_plan');
            $table->integer('product_tax_id')->default(0);
            $table->string('product_template', 64)->default('default');
            $table->string('product_url', 128);
            $table->decimal('product_old_price', 12, 2)->default(0.00);
            $table->decimal('product_buy_price', 12, 2)->default(0.00);
            $table->decimal('product_price', 12, 2)->default(0.00);
            $table->decimal('retail_price', 12, 2);
            $table->boolean('special_price');
            $table->decimal('min_price', 12, 2)->default(0.00);
            $table->boolean('different_prices')->default(0);
            $table->decimal('product_weight', 14, 4);
            $table->string('product_thumb_image', 255);
            $table->string('product_name_image', 255);
            $table->string('product_full_image', 255);
            $table->integer('product_manufacturer_id')->default(0);
            $table->boolean('product_is_add_price')->default(0);
            $table->float('average_rating', 4, 2)->default(0.00);
            $table->integer('reviews_count')->default(0);
            $table->integer('delivery_times_id')->default(0);
            $table->integer('hits')->default(0);
            $table->decimal('weight_volume_units', 12, 4);
            $table->integer('basic_price_unit_id')->default(0);
            $table->integer('label_id');
            $table->integer('vendor_id')->default(0);

            // Multi-language fields
            $table->string('name_en-GB', 255);
            $table->string('alias_en-GB', 255);
            $table->text('short_description_en-GB');
            $table->text('description_en-GB');
            $table->string('meta_title_en-GB', 255);
            $table->text('meta_description_en-GB');
            $table->text('meta_keyword_en-GB');

            $table->string('name_uk-UA', 255);
            $table->string('alias_uk-UA', 255);
            $table->text('short_description_uk-UA');
            $table->text('description_uk-UA');
            $table->string('meta_title_uk-UA', 255);
            $table->text('meta_description_uk-UA');
            $table->text('meta_keyword_uk-UA');

            $table->string('name_ru-UA', 255);
            $table->string('alias_ru-UA', 255);
            $table->text('short_description_ru-UA');
            $table->text('description_ru-UA');
            $table->string('meta_title_ru-UA', 255);
            $table->text('meta_description_ru-UA');
            $table->text('meta_keyword_ru-UA');

            $table->string('name_ru-UA', 255);
            $table->string('alias_ru-UA', 255);
            $table->text('short_description_ru-UA');
            $table->text('description_ru-UA');
            $table->string('meta_title_ru-UA', 255);
            $table->text('meta_description_ru-UA');
            $table->text('meta_keyword_ru-UA');

            $table->string('image', 255);
            $table->string('name_langmetadata', 255);
            $table->string('alias_langmetadata', 255);
            $table->text('short_description_langmetadata');
            $table->text('description_langmetadata');
            $table->string('meta_title_langmetadata', 255);
            $table->text('meta_description_langmetadata');
            $table->text('meta_keyword_langmetadata');

            $table->integer('parent_id');
            $table->integer('currency_id')->default(0);
            $table->integer('access')->default(1);
            $table->integer('add_price_unit_id')->default(0);

            // Auction fields
            $table->boolean('use_auction')->default(0);
            $table->boolean('use_buy_now')->default(0);
            $table->datetime('auction_start')->nullable();
            $table->datetime('auction_end')->nullable();
            $table->boolean('auction_finished')->default(1);
            $table->boolean('auction_notify')->default(0);
            $table->decimal('auction_price', 12, 2);
            $table->integer('auction_id')->default(0);
            $table->decimal('bid_increments', 12, 2);

            // Additional fields
            $table->text('characteristics_en-GB');
            $table->text('delivery_kit_en-GB');
            $table->text('video_review_en-GB');
            $table->text('documentation_en-GB');
            $table->text('articles_en-GB');
            $table->text('characteristics_uk-UA');
            $table->text('delivery_kit_uk-UA');
            $table->text('video_review_uk-UA');
            $table->text('documentation_uk-UA');
            $table->text('articles_uk-UA');
            $table->text('characteristics_ru-UA');
            $table->text('delivery_kit_ru-UA');
            $table->text('video_review_ru-UA');
            $table->text('documentation_ru-UA');
            $table->text('articles_ru-UA');
            $table->text('full_description_en-GB');
            $table->text('full_description_uk-UA');
            $table->text('full_description_ru-UA');
            $table->date('hit_date')->nullable();
            $table->date('start_hit_date')->nullable();
            $table->integer('hits_num');
            $table->text('calc_ru-UA');
            $table->text('calc_uk-UA');
            $table->text('calc_en-GB');
            $table->integer('asset_id');
            $table->datetime('comingsoon_date')->nullable();
            $table->boolean('addon_prod_availability_custom_status');

            // Indexes
            $table->index('product_manufacturer_id');
            $table->index('product_price');
            $table->index('product_publish');
            $table->index('hits');
            $table->index('average_rating');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
