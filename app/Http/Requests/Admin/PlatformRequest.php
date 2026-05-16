<?php

namespace App\Http\Requests\Admin;

use App\Rules\RichImageInputRule;
use Illuminate\Foundation\Http\FormRequest;

class PlatformRequest extends FormRequest
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
        $model = $this->route('platform');
        $modelId = $model ? "$model->id" : 'NULL';

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', "unique:platforms,slug,$modelId", 'string', 'max:255'],
            'icon' => [new RichImageInputRule(! $model)],
        ];
    }
}
