<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
        ];
    }

    public function locale($locale)
    {
        return $this->state(function (array $attributes) use ($locale) {
            return [
                'locale' => $locale,
            ];
        });
    }

    public function field($field)
    {
        return $this->state(function (array $attributes) use ($field) {
            return [
                'field' => $field,
            ];
        });
    }

    public function value($value)
    {
        return $this->state(function (array $attributes) use ($value) {
            return [
                'value' => $value,
            ];
        });
    }

    public function text($len = 30)
    {
        return $this->state(function (array $attributes) use ($len) {
            $faker = \Faker\Factory::create();

            return [
                'value' => $faker->realText($len),
            ];
        });
    }
}
