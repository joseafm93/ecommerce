<?php

namespace Tests;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Size;
use App\Models\Subcategory;
use Faker\Factory;

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

    protected function createColor()
    {
        return Color::create([
           'name' => Factory::create()->colorName
        ]);
    }

    protected function createImage($imageableId, $imageableType)
    {
        return Image::factory(4)->create([
            'imageable_id' => $imageableId,
            'imageable_type' => $imageableType
        ]);
    }

    protected function createBrand()
    {
        $brand = Brand::factory()->create();
        $category = Category::find($categoryId);
        $category->brands()->attach($brand);

        return $brand;
    }

    protected function createSize()
    {
        $size = Size::factory()->create([
            'product_id' => $productId
        ]);


        foreach ($colors as $color) {
            $size->colors()->attach([
                $color->id => ['quantity' => 12]
            ]);
        }

        return $size;
    }
}
