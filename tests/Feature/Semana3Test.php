<?php

namespace Tests\Feature;

use App\Http\Livewire\AddCartItemColor;
use App\Http\Livewire\AddCartItemSize;
use App\Http\Livewire\Search;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\TestHelpers;

class Semana3Test extends TestCase
{
    use RefreshDatabase, TestHelpers;

    /** @test */
    public function we_can_see_the_stock_available()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $subcategory2 = $this->createSubcategory($category->id, true);
        $subcategory3 = $this->createSubcategory($category->id, true, true);

        $brand = $this->createBrand($category->id);

        $color = $this->createColor();

        $product = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory2->id, $brand->id, Product::PUBLICADO, array($color));
        $product3 = $this->createProduct($subcategory3->id, $brand->id, Product::PUBLICADO, array($color));

        $size = $this->createSize($product3->id, array($color));

        $this->get('/products/' . $product->slug)
            ->assertStatus(200)
            ->assertSeeText('Stock disponible:')
            ->assertSee($product->quantity);

        $this->assertEquals(qty_available($product->id), 15);

        Livewire::test(AddCartItemColor::class, ['product' => $product2])
            ->set('options', ['color_id' => $color->id])
            ->call('addItem');

        $this->assertEquals(qty_available($product2->id, $color->id), 9);


        Livewire::test(AddCartItemSize::class, ['product' => $product3])
            ->set('options', ['size_id' => $size->id, 'color_id' => $color->id])
            ->call('addItem');

        $this->assertEquals(qty_available($product3->id, $color->id, $size->id), 11);
    }

    /** @test */
    public function search_input_can_filter_or_shows_nothing_if_input_is_empty()
    {
        $category = Category::factory()->create();
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false,
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach([$brand->id]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Pepe',
            'brand_id' => $brand->id,
            'quantity' => 2
        ]);
        Image::factory(2)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Pepito',
            'brand_id' => $brand->id,
            'quantity' => 2
        ]);
        Image::factory(2)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        Livewire::test(Search::class)
            ->set('search', 'Pepi')
            ->assertSee($product2->name)
            ->assertDontSee($product->name);

        Livewire::test(Search::class)
            ->set('search', '')
            ->assertDontSee($product->name)
            ->assertDontSee($product2->name);
    }
}
