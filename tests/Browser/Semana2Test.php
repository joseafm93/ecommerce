<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Semana2Test extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test  */
    public function a_user_not_logged_can_see_the_login_link()
    {
        $category = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit('/')
                    ->click('@perfil')
                    ->assertSeeLink('Iniciar sesión')
                    ->assertSeeLink('Registrarse')
                    ->screenshot('login');
        });
    }

    /** @test  */
    public function a_logged_user_can_see_the_logout_link()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $user) {
            $browser->loginAs($user)->visit('/')
                ->click('@perfil')
                ->assertSeeLink('Perfil')
                ->assertSeeLink('Finalizar sesión')
                ->screenshot('logout');
        });
    }

    /** @test  */
    public function it_can_shows_at_least_5_products_at_the_home_page()
    {
        $category = Category::factory()->create();
        $product = $this->createProduct($category);
        $product2 = $this->createProduct($category);
        $deleted = $this->createProduct($category, Product::BORRADOR);

        $this->browse(function (Browser $browser) use ($product, $product2, $deleted) {
            $browser->visit('/')
                ->pause(500)
                ->assertSee($product->name)
                ->assertSee($product2->name)
                ->assertDontSee($deleted->name)
                ->screenshot('5products');
        });
    }

    /** @test  */
    public function it_can_see_the_category_details_page()
    {
        $category = Category::factory()->create();

        $product = $this->createProduct($category);
        $product2 = $this->createProduct($category);

        $this->browse(function (Browser $browser) use ($category, $product, $product2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSee('Subcategorías')
                ->assertSee('Marcas')
                ->assertSee($product->name)
                ->assertSee($product2->name)
                ->screenshot('category-details');
        });
    }

    /** @test  */
    public function it_can_filter_by_subcategory_at_the_category_details_page()
    {
        $category = Category::factory()->create();

        $product = $this->createProduct($category);
        $product2 = $this->createProduct($category);

        $this->browse(function (Browser $browser) use ($category, $product, $product2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSee('Subcategorías')
                ->assertSee('Marcas')
                ->click('@filterSubcategory')
                ->assertSee($product->name)
                ->assertDontSee($product2->name)
                ->screenshot('filter-by-sub-at-category-details');
        });
    }

    /** @test  */
    public function it_can_filter_by_brand_at_the_category_details_page()
    {
        $category = Category::factory()->create();

        $product = $this->createProduct($category);
        $product2 = $this->createProduct($category);

        $this->browse(function (Browser $browser) use ($category, $product, $product2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSee('Subcategorías')
                ->assertSee('Marcas')
                ->click('@filterBrand')
                ->assertSee($product->name)
                ->assertDontSee($product2->name)
                ->screenshot('filter-by-brand-at-category-details');
        });
    }

    /** @test  */
    public function it_can_shows_the_product_details_page()
    {
        $category = Category::factory()->create();

        $product = $this->createProduct($category);

        $this->browse(function (Browser $browser) use ($category, $product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertSee($product->name)
                ->assertSee($product->description)
                ->assertSee($product->price)
                ->pause(500)
                ->assertSee($product->quantity)
                ->assertVisible('@productImage')
                ->assertVisible('@incrementButton')
                ->assertVisible('@decrementButton')
                ->assertVisible('@addItemToCart')
                ->screenshot('product-details-page');
        });
    }

    /** @test  */
    public function the_limit_of_the_increment_button_is_the_max_quantity()
    {
        $category = Category::factory()->create();
        $product = $this->createProduct($category);

        $this->browse(function (Browser $browser) use ($category, $product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertButtonEnabled('@incrementButton')
                ->press('@incrementButton')
                ->pause(500)
                ->press('@incrementButton')
                ->pause(500)
                ->assertButtonDisabled('@incrementButton')
                ->screenshot('increment-button-limit');
        });
    }

    /** @test  */
    public function the_limit_of_the_decrement_button_is_1()
    {
        $category = Category::factory()->create();
        $product = $this->createProduct($category);

        $this->browse(function (Browser $browser) use ($category, $product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertButtonDisabled('@decrementButton')
                ->press('@incrementButton')
                ->pause(500)
                ->assertButtonEnabled('@decrementButton')
                ->screenshot('decrement-button-limit');
        });
    }

    /** @test  */
    public function visible_size_and_color_select_depending_its_subcategory()
    {
        $brand = Brand::factory()->create();

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $category3 = Category::factory()->create();

        $category1->brands()->attach($brand->id);
        $category2->brands()->attach($brand->id);
        $category3->brands()->attach($brand->id);

        $subcategory1 = Subcategory::factory()->create([
            'category_id' => $category1->id,
            'color' => false,
            'size' => false
        ]);

        $subcategory2 = Subcategory::factory()->create([
            'category_id' => $category2->id,
            'color' => true,
            'size' => false
        ]);

        $subcategory3 = Subcategory::factory()->create([
            'category_id' => $category3->id,
            'color' => true,
            'size' => true
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory1->id,
            'quantity' => 10
        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class,
        ]);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory2->id,
            'quantity' => 10
        ]);

        Image::factory()->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class,
        ]);

        $product3 = Product::factory()->create([
            'subcategory_id' => $subcategory3->id,
            'quantity' => 10
        ]);

        Image::factory()->create([
            'imageable_id' => $product3->id,
            'imageable_type' => Product::class,
        ]);

        $this->browse(function (Browser $browser) use ($product1, $product2, $product3) {
            $browser->visit('/products/' . $product1->slug)
                ->pause(500)
                ->assertNotPresent('@colorSelect')
                ->assertNotPresent('@sizeSelect')
                ->screenshot('notcolornorsize');

            $browser->visit('/products/' . $product2->slug)
                ->pause(500)
                ->assertPresent('@colorSelect')
                ->assertNotPresent('@sizeSelect')
                ->screenshot('onlycolor');

            $browser->visit('/products/' . $product3->slug)
                ->pause(500)
                ->assertPresent('@colorSelect')
                ->pause(500)
                ->assertPresent('@sizeSelect')
                ->screenshot('bothcolorandsize');
        });
    }

    private function createProduct($category, $status = Product::PUBLICADO, $size = false, $color = false) {
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
