<?php

namespace Tests;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\User;
use Faker\Factory;
use Spatie\Permission\Models\Role;

trait TestHelpers
{
    protected function createCategory()
    {
        return Category::factory()->create();
    }

    protected function createSubcategory($categoryId, $hasColor = false, $hasSize = false)
    {
        return Subcategory::factory()->create([
            'category_id' => $categoryId,
            'color' => $hasColor,
            'size' => $hasSize
        ]);
    }

    protected function createBrand($categoryId)
    {
        $brand = Brand::factory()->create();
        $category = Category::find($categoryId);
        $category->brands()->attach($brand);

        return $brand;
    }

    protected function createColor()
    {
        return Color::create([
            'name' => Factory::create()->colorName()
        ]);
    }

    protected function createImage($imageableId, $imageableType)
    {
        return Image::factory(4)->create([
            'imageable_id' => $imageableId,
            'imageable_type' => $imageableType
        ]);
    }

    protected function createSize($productId)
    {
        $size = Size::factory()->create([
            'product_id' => $productId
        ]);

        return $size;
    }

    protected function createProduct($subcategoryId, $brandId, $status = Product::PUBLICADO, $colors = null)
    {
        $subcategory = Subcategory::find($subcategoryId);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategoryId,
            'brand_id' => $brandId,
            'quantity' => $subcategory->color ? null : 15,
            'status' => $status,
        ]);

        if ($subcategory->color && !$subcategory->size && is_array($colors)) {
            foreach ($colors as $color) {
                $product->colors()->attach([
                   $color->id => ['quantity' => 10]
                ]);
            }
        }

        $this->createImage($product->id, Product::class);

        return $product;
    }

    protected function createProduct3($subcategoryId, $brandId, $status = Product::PUBLICADO, $colors = null)
    {
        $subcategory = Subcategory::find($subcategoryId);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategoryId,
            'brand_id' => $brandId,
            'quantity' => $subcategory->color ? null : 3,
            'status' => $status,
        ]);

        if ($subcategory->color && !$subcategory->size && is_array($colors)) {
            foreach ($colors as $color) {
                $product->colors()->attach([
                    $color->id => ['quantity' => '3']
                ]);
            }
        }

        $this->createImage($product->id, Product::class);

        return $product;
    }

    protected function createAdminUser()
    {
        $adminRole = Role::create(['name' => 'admin']);

        $user = User::factory()->create();
        $user->assignRole($adminRole);

        return $user;
    }

    protected function createProductAll($hasColor = false, $hasSize = false, $status = Product::PUBLICADO)
    {
        $category = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id, $hasColor, $hasSize);

        $brand = $this->createBrand($category->id);
        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => $subcategory->color ? null : 3,
            'status' => $status,
            'price' => 84.99,
        ]);

        $this->createImage($product->id, Product::class);

        if ($hasColor && !$hasSize) {
            $color = $this->createColor();
            $product->colors()->attach($color->id, ['quantity' => '3']);
        }
        if ($hasColor && $hasSize) {
            $color = $this->createColor();
            $size = $this->createSize($product->id);
            $size->colors()->attach([$color->id => ['quantity' => 12]]);
        }

        return $product;
    }
}
