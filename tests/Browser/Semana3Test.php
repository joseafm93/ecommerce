<?php

namespace Tests\Browser;

use App\Http\Livewire\AddCartItem;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Department;
use App\Models\District;
use App\Models\Image;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Livewire\Livewire;
use Tests\DuskTestCase;

class Semana3Test extends DuskTestCase
{
    use DatabaseMigrations;

    /*Todos los test de esta clase refactorizados*/

    /** @test */
    public function a_product_without_size_or_color_can_be_added_to_cart()
    {
        $product = $this->createProductAll();

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertButtonEnabled('@addItemToCart')
                ->click('@addItemToCart')
                ->click('@dropdownCart')
                ->pause(500)
                ->assertSee($product->name)
                ->screenshot('product-can-be-added-to-cart');
        });
    }

    /** @test */
    public function a_product_with_color_can_be_added_to_cart()
    {
        $product = $this->createProductAll(true);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertButtonDisabled('@ColorAddItemToCart')
                ->click('@colorSelect')
                ->click('@color', 1)
                ->pause(100)
                ->assertButtonEnabled('@ColorAddItemToCart')
                ->click('@ColorAddItemToCart')
                ->click('@dropdownCart')
                ->pause(500)
                ->assertSee($product->name)
                ->screenshot('product-with-color-can-be-added-to-cart');
        });
    }

    /** @test */
    public function a_product_with_color_and_size_can_be_added_to_cart()
    {
        $product = $this->createProductAll(true, true);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertButtonDisabled('@ColorSizeAddItemToCart')
                ->click('@sizeSelect')
                ->pause(100)
                ->click('@size', 1)
                ->pause(100)
                ->click('@colorSizeSelect')
                ->click('@sizeColor', 1)
                ->pause(500)
                ->assertButtonEnabled('@ColorSizeAddItemToCart')
                ->click('@ColorSizeAddItemToCart')
                ->click('@dropdownCart')
                ->pause(500)
                ->assertSee($product->name)
                ->screenshot('product-with-color-and-size-can-be-added-to-cart');
        });
    }

    /** @test */
    public function the_red_circle_increments_when_adding_a_product()
    {
        $product = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertVisible('@cart0')
                ->assertNotPresent('@cart+')
                ->click('@addItemToCart')
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/')
                ->pause(500)
                ->click('@dropdownCart')
                ->assertSee('3')
                ->screenshot('cart-red-circle');
        });
    }

    /** @test */
    public function the_limit_of_stock_is_the_limit_of_adding_that_product_to_the_cart()
    {
        $product = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@incrementButton')
                ->pause(500)
                ->click('@incrementButton')
                ->click('@addItemToCart')
                ->pause(500)
                ->assertButtonDisabled('@addItemToCart')
                ->screenshot('stock-limit-adding-to-cart');
        });
    }

    /** @test */
    public function we_can_see_the_products_in_the_cart()
    {
        $product = $this->createProductAll();
        $product2 = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product, $product2) {
           $browser->visit('/products/' . $product->slug)
               ->pause(500)
               ->click('@addItemToCart')
               ->visit('/products/' . $product2->slug)
               ->pause(500)
               ->click('@addItemToCart')
               ->pause(500)
               ->visit('/shopping-cart')
               ->pause(500)
               ->assertSee('CARRITO DE COMPRAS')
               ->assertSee($product->name)
               ->assertSee($product2->name)
               ->screenshot('see-products-in-cart');
        });
    }

    /** @test */
    public function we_can_change_the_product_quantity_in_the_cart()
    {
        $product = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee('CARRITO DE COMPRAS')
                ->assertSee($product->name)
                ->assertSee($product->price)
                ->click('@cartIncrementButton')
                ->pause(200)
                ->assertSee('Total: ' . $product->price * 2 . ' €')
                ->screenshot('change-product-quantity-in-cart');
        });
    }

    /** @test */
    public function we_can_delete_a_product_or_remove_all_in_the_cart()
    {
        $product = $this->createProductAll();
        $product2 = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product, $product2) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee('CARRITO DE COMPRAS')
                ->assertSee($product->name)
                ->click('@deleteProduct')
                ->pause(500)
                ->assertNotPresent($product->name)
                ->assertSee('TU CARRITO DE COMPRAS ESTÁ VACÍO')
                ->screenshot('delete-a-product-cart');

            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/products/' . $product2->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee('CARRITO DE COMPRAS')
                ->assertSee($product->name)
                ->assertSee($product2->name)
                ->pause(200)
                ->click('@destroyCart')
                ->pause(200)
                ->assertSee('TU CARRITO DE COMPRAS ESTÁ VACÍO')
                ->screenshot('destroy-cart');
        });
    }

    /** @test */
    public function only_a_logged_user_can_make_orders()
    {
        $product = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee('CARRITO DE COMPRAS')
                ->assertSee($product->name)
                ->click('@continueToOrder')
                ->pause(500)
                ->assertPathIs('/login')
                ->screenshot('not-logged-user-make-order');

            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee('CARRITO DE COMPRAS')
                ->assertSee($product->name)
                ->pause(200)
                ->click('@continueToOrder')
                ->pause(500)
                ->assertPathIs('/orders/create')
                ->screenshot('logged-user-make-order');
        });
    }

    /** @test */
    public function the_cart_is_saved_on_DB_when_log_out()
    {
        $product = $this->createProductAll();

        /*Aqui creo una variable usuario para loguear las 2 veces con el mismo usuario*/
        $user = User::factory()->create();

        $this->assertDatabaseCount('shoppingcart', 0);

        $this->browse(function (Browser $browser) use ($product, $user) {
            $browser->loginAs($user)
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee('CARRITO DE COMPRAS')
                ->assertSee($product->name)
                ->logout()
                ->loginAs($user)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee($product->name)
                ->screenshot('cart-saved-DB');
        });

        $this->assertDatabaseCount('shoppingcart', 1);
    }

    /** @test */
    public function form_is_visible_only_when_delivery_option_is_selected()
    {
        $product = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->visit('/orders/create')
                ->pause(500)
                ->assertDontSee('Departamento')
                ->radio('envio_type', 1)
                ->pause(500)
                ->assertDontSee('Departamento')
                ->radio('envio_type', 2)
                ->pause(500)
                ->assertSee('Departamento')
                ->screenshot('form-delivery-option-order')
                ->radio('envio_type', 1)
                ->pause(500)
                ->assertDontSee('Departamento');
        });
    }

    /** @test */
    public function the_order_is_made_and_the_cart_is_destroyed()
    {
        $product = $this->createProductAll();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->visit('/orders/create')
                ->pause(500)
                ->type('@contactName', 'a')
                ->type('@contactPhone', '1')
                ->click('@createOrder')
                ->pause(500)
                ->assertPathIs('/orders/1/payment')
                ->click('@dropdownCart')
                ->pause(500)
                ->assertSee('No tiene agregado ningún item en el carrito')
                ->screenshot('order-made');
        });
    }

    /** @test */
    public function department_select_has_all_departments()
    {
        $product = $this->createProductAll();

        $departments = Department::factory(2)->create()->pluck('id')->all();

        $this->browse(function (Browser $browser) use ($product, $departments) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->visit('/orders/create')
                ->pause(500)
                ->radio('envio_type', 2)
                ->pause(500)
                ->click('@departmentSelect')
                ->pause(500)
                ->assertSelectHasOptions('departments', $departments)
                ->screenshot('department-select');
        });
    }

    /** @test */
    public function cities_select_has_cities_from_its_department()
    {
        $product = $this->createProductAll();

        $departments = Department::factory(2)->create();
        $cities= City::factory(2)->create([
            'department_id'=> $departments[0]->id
        ]);
        $cities2= City::factory(2)->create([
            'department_id'=> $departments[1]->id
        ]);
        $idCities = $cities->pluck('id')->all();
        $idCities2 = $cities2->pluck('id')->all();

        $this->browse(function (Browser $browser) use ($product, $idCities, $idCities2) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->visit('/orders/create')
                ->pause(500)
                ->click('@delivery')
                ->select('departments', 2)
                ->pause(500)
                ->click('@citySelect')
                ->assertSelectHasOptions('cities', $idCities2)
                ->assertSelectMissingOptions('cities', $idCities)
                ->screenshot('cities-select');
        });
    }

    /** @test */
    public function districts_select_has_districts_from_its_city()
    {
        $product = $this->createProductAll();

        $departments = Department::factory(2)->create();
        $cities= City::factory(2)->create([
            'department_id'=> $departments[0]->id
        ]);
        $districts = District::factory(2)->create([
            'city_id'=>$cities[0]->id
        ]);
        $districts2 = District::factory(2)->create([
            'city_id'=>$cities[1]->id
        ]);

        $idDistricts = $districts->pluck('id')->all();
        $idDistricts2 = $districts2->pluck('id')->all();

        $this->browse(function (Browser $browser) use ($product, $idDistricts, $idDistricts2) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->visit('/orders/create')
                ->pause(500)
                ->click('@delivery')
                ->select('departments', 1)
                ->pause(500)
                ->select('cities', 1)
                ->pause(500)
                ->click('@districtSelect')
                ->pause(500)
                ->assertSelectHasOptions('districts', $idDistricts)
                ->assertSelectMissingOptions('districts', $idDistricts2)
                ->screenshot('districts-select');
        });
    }
}
