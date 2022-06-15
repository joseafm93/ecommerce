<?php

namespace Tests\Feature;

use App\Http\Livewire\AddCartItem;
use App\Listeners\MergeTheCart;
use App\Models\Product;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\TestHelpers;

class Ej1ExamenTest extends TestCase
{
    use DatabaseMigrations, TestHelpers;

    /** @test Ejercicio1 Examen */
    public function ej1_examen()
    {
        $user = User::factory()->create();

        $product = $this->createProductAll();

        $product2 = $this->createProductAll();

        $this->actingAs($user);

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        $content = Cart::content();

        $this->post('/logout');

        $this->assertDatabaseHas('shoppingcart', ['content' => serialize($content)]);

        $cart = new MergeTheCart();
        $userLogin = new Login('web', $user, true);
        $this->actingAs($user);

        $cart->handle($userLogin);

        $this->get('/orders/create')
            ->assertStatus(200)
            ->assertSee($product->name)
            ->assertSee($product->price)
            ->assertSee($product->quantity)
            ->assertSee($product2->name)
            ->assertSee($product2->price)
            ->assertSee($product2->quantity);
    }
}
