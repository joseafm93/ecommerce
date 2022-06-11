<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Semana4Test extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function we_cannot_access_to_routes_that_we_must_be_authenticated()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $user){
            $browser->visit('/orders')
                ->assertPathIs('/login')
                ->screenshot('routes-1')
                ->visit('/orders/create')
                ->assertPathIs('/login')
                ->screenshot('routes-2');

            $browser->loginAs($user)
                ->visit('/orders')
                ->assertPathIs('/orders')
                ->screenshot('routes-3')
                ->visit('/orders/create')
                ->assertPathIs('/orders/create')
                ->screenshot('routes-4');
        });
    }

    /** @test */
    public function a_user_cannot_see_other_users_orders()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $product, $user, $user2) {
            $browser->loginAs($user)
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->visit('/orders/create')
                ->pause(500)
                ->type('@contactName', 'a')
                ->type('@contactPhone', '1')
                ->click('@createOrder')
                ->logout();

            $order = Order::first();

            $browser->loginAs($user2)
                ->visit('orders/' . $order->id . '/payment')
                ->assertSee('ESTA ACCIÓN NO ESTÁ AUTORIZADA')
                ->screenshot('user-sees-other-user-order');
        });
    }

    /** @test */
    public function a_user_can_see_his_own_orders()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $product, $user) {
            $browser->loginAs($user)
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->visit('/orders/create')
                ->pause(500)
                ->type('@contactName', 'a')
                ->type('@contactPhone', '1')
                ->click('@createOrder')
                ->pause(500)
                ->press('@userDropdown')
                ->pause(500)
                ->press('@myOrders')
                ->pause(500)
                ->assertPathIs('/orders')
                ->screenshot('user-sees-his-orders');
        });
    }

    /** @test */
    public function stock_decrements_when_creating_an_order()
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

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $product, $product2 ,$product3 ,$user) {
            $browser->loginAs($user)
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->assertSeeIn('@stock', 15)
                ->click('@addItemToCart')
                ->pause(500)
                ->assertSeeIn('@stock', 14)
                ->screenshot('stock-decrements-order');

            $browser->visit('/products/' . $product2->slug)
                ->pause(500)
                ->assertSeeIn('@colorStock', 10)
                ->select('@colorSelect', 1)
                ->pause(500)
                ->click('@ColorAddItemToCart')
                ->pause(500)
                ->assertSeeIn('@colorStock', 9)
                ->screenshot('stock-decrements-order-color');

            $browser->visit('/products/' . $product3->slug)
                ->pause(500)
                ->assertSeeIn('@sizeStock', 12)
                ->select('@sizeSelect', 1)
                ->pause(500)
                ->select('@colorSizeSelect', 1)
                ->pause(500)
                ->click('@ColorSizeAddItemToCart')
                ->pause(500)
                ->assertSeeIn('@sizeStock', 11)
                ->screenshot('stock-decrements-order-size');
        });
    }

    /** @test */
    public function stock_decrements_in_DB()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $product, $user) {
            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'quantity' => $product->quantity,
            ]);

            $browser->loginAs($user)
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->screenshot('stock-decrements-BD')
                ->visit('/orders/create')
                ->pause(500)
                ->type('@contactName', 'a')
                ->type('@contactPhone', '1')
                ->click('@createOrder')
                ->pause(500);

            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'quantity' => $product->quantity -1,
            ]);
        });
    }

    /** @test */
    public function order_is_cancelled_over_10_minutes()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $product, $user) {
            $browser->loginAs($user)
                ->visit('/products/' . $product->slug)
                ->pause(500)
                ->click('@addItemToCart')
                ->pause(500)
                ->screenshot('stock-decrements-BD')
                ->visit('/orders/create')
                ->pause(500)
                ->type('@contactName', 'a')
                ->type('@contactPhone', '1')
                ->click('@createOrder')
                ->pause(500);

            $order = Order::first();
            $order->created_at = now()->subMinutes(11);
            $order->save();

            $this->artisan('schedule:run');

            $this->assertDatabaseHas('orders', [
                'id' => $order->id,
                'user_id' => $user->id,
                'status' => '5',
            ]);

            $browser->loginAs($user)
                ->visit('/orders')
                ->pause(1000)
                ->screenshot('order-cancelled-10-min');
        });
    }

    /** @test */
    public function check_the_admin_search_input()
    {
        $category = Category::factory()->create();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory->id, $brand->id);

        $adminUser = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($category, $product, $product2, $adminUser) {
            $browser->loginAs($adminUser)
                ->visit('/admin')
                ->pause(500)
                ->type('@adminSearch', $product->name)
                ->pause(200)
                ->assertSee($product->name)
                ->assertDontSee($product2->name)
                ->screenshot('admin-search-input');
        });
    }
}
