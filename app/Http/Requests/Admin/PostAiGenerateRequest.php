<?php

namespace App\Http\Requests\Admin;

use App\Factories\AiModelFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostAiGenerateRequest extends FormRequest
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
        $models = (new AiModelFactory)->available();
        $models = collect($models)->pluck('id');

        return [
            'title' => ['required', 'string', 'max:255'],
            'structure' => ['required', 'string', 'max:5000'],
            'prompt' => ['required', 'string', 'max:2000'],
            'save_prompt' => ['required', 'boolean'],
            'save_prompt_name' => ['nullable', 'required_if:save_prompt,1', 'string', 'max:255'],
            'model' => ['required', 'string', Rule::in($models)],
        ];
    }
}
