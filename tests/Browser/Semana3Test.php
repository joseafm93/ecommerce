<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Semana3Test extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_product_without_size_or_color_can_be_added_to_cart()
    {
        $category = Category::factory()->create();
        $product = $this->createProduct($category);

        $this->browse(function (Browser $browser) use ($category, $product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertButtonEnabled('@addItemToCart')
                ->click('@addItemToCart')
                ->pause(500)
                ->click('@dropdownCart')
                ->screenshot('product-can-be-added-to-cart');
        });
    }

    /** @test */
    public function a_product_with_color_can_be_added_to_cart()
    {
        $category = Category::factory()->create();
        $product = $this->createProduct($category, Product::PUBLICADO, true);

        $this->browse(function (Browser $browser) use ($category, $product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertButtonDisabled('@ColorAddItemToCart')
                ->screenshot('product-with-color-can-be-added-to-cart');
        });
    }

    private function createProduct($category, $status = Product::PUBLICADO, $color = false, $size = false) {
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => $color,
            'size' => $size
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'status' => $status,
            'quantity' => 3,
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        return $product;
    }
}
