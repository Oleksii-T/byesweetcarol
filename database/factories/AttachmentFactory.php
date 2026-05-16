<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'no-thumbnail.jpeg',
            'original_name' => 'no-thumbnail.jpeg',
            'type' => 'image',
            'size' => '7122',
        ];
    }

    public function group($group)
    {
        return $this->state(function (array $attributes) use ($group) {
            return [
                'group' => $group,
            ];
        });
    }

    public function parent($model)
    {
        return $this->state(function (array $attributes) use ($model) {
            return [
                'translatable_id' => $model->id,
                'translatable_type' => get_class($model),
            ];
        });
    }
}
