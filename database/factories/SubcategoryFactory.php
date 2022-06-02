<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category = Category::all()->random();
        $color = collect([true, false])->random();
        $size = collect([true, false])->random();
        $name = $this->faker->sentence(2);

        return [
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => $color,
            'size' => $color ? $size : false,
        ];
    }
}
