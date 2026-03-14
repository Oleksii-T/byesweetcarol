<?php

namespace App\Http\Requests\Admin;

use App\Rules\RichImageInputRule;
use Illuminate\Foundation\Http\FormRequest;

class GameRequest extends FormRequest
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
        $model = $this->route('game');
        $reqNull = $model ? 'nullable' : 'required';

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string'],
            'meta_title' => ['required', 'string', 'max:255'],
            'meta_description' => ['required', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'metacritic' => ['nullable', 'integer', 'min:0', 'max:100'],
            'users_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'release_date' => ['nullable', 'string', 'max:255'],
            'developer' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'platforms' => ['nullable', 'array'],
            'platforms.*' => ['required', 'integer'],
            'ganres' => ['required', 'string'],
            'thumbnail' => [new RichImageInputRule],
            'description' => ['required', 'string', 'max:10000'],
            'summary' => ['nullable', 'string', 'max:10000'],
            'official_site' => ['nullable', 'string'],
            'steam' => ['nullable', 'string'],
            'playstation_store' => ['nullable', 'string'],
            'xbox_store' => ['nullable', 'string'],
            'nintendo_store' => ['nullable', 'string'],
            'esbr' => ['nullable', 'string', 'max:10000'],
            'esbr_image' => [new RichImageInputRule(false)],
            'hours' => ['required', 'array'],
            'hours.main' => ['nullable', 'numeric'],
            'hours.main_sides' => ['nullable', 'numeric'],
            'hours.completionist' => ['nullable', 'numeric'],
            'hours.all' => ['nullable', 'numeric'],
            'screenshots' => [new RichImageInputRule(false, true)],
        ];
    }
}
