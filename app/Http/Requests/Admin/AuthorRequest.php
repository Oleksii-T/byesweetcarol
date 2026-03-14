<?php

namespace App\Http\Requests\Admin;

use App\Rules\RichImageInputRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
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
        $model = $this->route('author');
        $reqNull = $model ? 'nullable' : 'required';
        // dd(request()->all());

        return [
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'avatar' => [new RichImageInputRule],
            'email' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'description_small' => ['required', 'string', 'max:10000'],
            'meta_thumbnail' => [new RichImageInputRule],
            // 'meta_thumbnail' => ['required', 'array'],
            // 'meta_thumbnail.file' => [$reqNull, 'file', 'max:5000'],
            // 'meta_thumbnail.alt' => [$reqNull, 'string', 'max:255'],
            // 'meta_thumbnail.title' => [$reqNull, 'string', 'max:255'],
            // 'meta_thumbnail.id' => ['nullable', 'integer'],
            'meta_description' => ['required', 'string', 'max:255'],
            'meta_title' => ['required', 'string', 'max:255'],
        ];
    }
}
