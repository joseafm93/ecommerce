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
use Illuminate\Support\Str;
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
                    ->pause(500)
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

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory->id, $brand->id);
        $product3 = $this->createProduct($subcategory->id, $brand->id);
        $product4 = $this->createProduct($subcategory->id, $brand->id);
        $product5 = $this->createProduct($subcategory->id, $brand->id);

        $this->browse(function (Browser $browser) use ($product, $product2, $product3, $product4, $product5) {
            $browser->visit('/')
                ->pause(500)
                ->assertSee(Str::limit($product->name,20))
                ->assertSee(Str::limit($product2->name,20))
                ->assertSee(Str::limit($product3->name,20))
                ->assertSee(Str::limit($product4->name,20))
                ->assertSee(Str::limit($product5->name,20))
                ->screenshot('5products');
        });
    }

    /** @test  */
    public function it_shows_only_published_products()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);
        $product3 = $this->createProduct($subcategory->id, $brand->id);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'status' => Product::BORRADOR,
        ]);

        $this->browse(function (Browser $browser) use ($category, $product, $product2, $product3) {
            $browser->visit('/')
                ->assertSee(Str::limit($product->name,20))
                ->assertDontSee($product2->name)
                ->assertSee(Str::limit($product3->name,20))
                ->screenshot('shows-only-published-products');
        });
    }

    /** @test  */
    public function it_can_see_the_category_details_page()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory->id, $brand->id);

        $this->browse(function (Browser $browser) use ($category, $product, $product2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSee('Subcategorías')
                ->assertSee('Marcas')
                ->assertSee(Str::limit($product->name,20))
                ->assertSee(Str::limit($product2->name,20))
                ->screenshot('category-details');
        });
    }

    /** @test  */
    public function it_can_filter_by_subcategory_at_the_category_details_page()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $subcategory2 = $this->createSubcategory($category->id);

        $brand = $this->createBrand($category->id);
        $brand2 = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory2->id, $brand2->id);

        $this->browse(function (Browser $browser) use ($category, $product, $product2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSee('Subcategorías')
                ->assertSee('Marcas')
                ->click('@filterSubcategory')
                ->assertSee(Str::limit($product->name,20))
                ->assertDontSee(Str::limit($product2->name,20))
                ->screenshot('filter-by-subcategory-at-category-details');
        });
    }

    /** @test  */
    public function it_can_filter_by_brand_at_the_category_details_page()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $subcategory2 = $this->createSubcategory($category->id);

        $brand = $this->createBrand($category->id);
        $brand2 = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory2->id, $brand2->id);

        $this->browse(function (Browser $browser) use ($category, $product, $product2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSee('Subcategorías')
                ->assertSee('Marcas')
                ->click('@filterBrand')
                ->assertSee(Str::limit($product->name,20))
                ->assertDontSee(Str::limit($product2->name,20))
                ->screenshot('filter-by-brand-at-category-details');
        });
    }

    /** @test  */
    public function it_can_shows_the_product_details_page()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

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

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct3($subcategory->id, $brand->id);

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

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

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
        $category = Category::factory()->create();

        $brand = $this->createBrand($category->id);

        $subcategory = $this->createSubcategory($category->id, true);
        $subcategory2 = $this->createSubcategory($category->id, true, true);

        $color = $this->createColor();

        $product = $this->createProduct($subcategory->id, $brand->id, Product::PUBLICADO, $color);
        $product2 = $this->createProduct($subcategory2->id, $brand->id, Product::PUBLICADO, $color);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        Image::factory()->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class,
        ]);

        $this->browse(function (Browser $browser) use ($product, $product2) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertPresent('@colorSelect')
                ->assertNotPresent('@sizeSelect')
                ->screenshot('onlycolor');

            $browser->visit('/products/' . $product2->slug)
                ->pause(500)
                ->assertPresent('@colorSelect')
                ->pause(500)
                ->assertPresent('@sizeSelect')
                ->screenshot('bothcolorandsize');
        });
    }
}
