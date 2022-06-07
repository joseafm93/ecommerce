<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Semana1Test extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A basic browser test example.
     *
     * @return void
     */

    /** @test  */
    public function it_shows_a_category_when_the_navigation_menu_is_clicked()
    {
        $category = Category::factory()->create([
            'name' => 'Pepe Pepito Pepote'
        ]);

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit('/')
                    ->click('@showcategory')
                    ->assertSee($category->name)
                    ->screenshot('category-showed-in-navigation-menu');
        });
    }

    /** @test  */
    public function it_shows_a_subcategory_when_the_navigation_menu_is_clicked_and_the_mouse_is_over_a_category()
    {
        $category = Category::factory()->create([
            'name' => 'Lolito'
        ]);

        $subcategory = Subcategory::factory()->create([
            'name' => 'Fernandez'
        ]);

        $this->browse(function (Browser $browser) use ($category, $subcategory) {
            $browser->visit('/')
                ->click('@showcategory')
                ->mouseover('@showsubcategory')
                ->assertSee($subcategory->name)
                ->screenshot('subcategory-showed-in-navigation-menu');
        });
    }
}
