<?php

namespace App\Http\Requests\Admin;

use App\Rules\RichImageInputRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $model = $this->route('category');
        $reqNull = $model ? 'nullable' : 'required';

        return [
            'in_menu' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'meta_thumbnail' => [new RichImageInputRule],
            'meta_description' => ['required', 'string', 'max:255'],
            'meta_title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
        ];
    }
}
