<?php

namespace Tests\Feature;

use App\Http\Livewire\Admin\CreateProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\TestHelpers;

class Semana4Test extends TestCase
{
    use RefreshDatabase, TestHelpers;

    /** @test */
    public function it_creates_a_product()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save');

        $this->assertDatabaseCount('products', 1);
    }

    /** @test */
    public function it_creates_a_product_with_color()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id, true);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save');

        $this->assertDatabaseCount('products', 1);
    }

    /** @test */
    public function it_creates_a_product_with_color_and_size()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id, true, true);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save');

        $this->assertDatabaseCount('products', 1);
    }

    /** @test */
    public function the_category_is_required()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', '')
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['category_id' => 'required']);
    }

    /** @test */
    public function the_subcategory_is_required()
    {
        $category = $this->createCategory();

        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', '')
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['subcategory_id' => 'required']);
    }

    /** @test */
    public function the_name_is_required()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', '')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function the_slug_is_required()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', '')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['slug' => 'required']);
    }

    /** @test */
    public function the_slug_is_unique()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save');

        $this->assertDatabaseCount('products', 1);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto2')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);

        $this->assertDatabaseCount('products', 1);
    }

    /** @test */
    public function the_description_is_required()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', '')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['description' => 'required']);
    }

    /** @test */
    public function the_brand_is_required()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', '')
            ->set('name', 'Producto')
            ->set('slug', '')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['brand_id' => 'required']);
    }

    /** @test */
    public function the_price_is_required()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', '')
            ->set('quantity', 8)
            ->call('save')
            ->assertHasErrors(['price' => 'required']);
    }

    /** @test */
    public function the_quantity_is_required_when_the_product_has_not_color_or_size()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', '')
            ->call('save')
            ->assertHasErrors(['quantity' => 'required']);
    }

    /** @test */
    public function the_quantity_is_optional_when_the_product_has_color_or_color_and_size()
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id, true, true);
        $brand = $this->createBrand($category->id);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category->id)
            ->set('subcategory_id', $subcategory->id)
            ->set('brand_id', $brand->id)
            ->set('name', 'Producto')
            ->set('slug', 'producto')
            ->set('description', 'descripcion')
            ->set('price', 29)
            ->set('quantity', '')
            ->call('save')
            ->assertHasNoErrors('quantity');

        $this->assertDatabaseCount('products', 1);
    }
}
