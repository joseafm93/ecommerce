<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Ej1ExamenTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test Ejercicio1 Examen */
    public function ej1_examen()
    {
        $product = $this->createProductAll();
        $product2 = $this->createProductAll();

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($product, $product2, $user) {
            $browser->loginAs($user)
                ->visit('/products/' . $product->slug)
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
                ->assertSee($product->price)
                ->assertSee($product2->price)
                ->logout()
                ->loginAs($user)
                ->visit('/shopping-cart')
                ->pause(500)
                ->assertSee($product->name)
                ->assertSee($product2->name)
                ->assertSee($product->price)
                ->assertSee($product2->price)
                ->screenshot('test-examen');
        });
    }
}
