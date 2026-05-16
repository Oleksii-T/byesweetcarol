<?php

namespace App\Http\Requests\Admin;

use App\Factories\TranslatorFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TranslateRequest extends FormRequest
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
        $translators = (new TranslatorFactory)->available();
        $translators = collect($translators)->pluck('id');

        return [
            'text' => ['required', 'string'],
            'language' => ['required', 'string', Rule::in(['en', 'ru'])],
            'translator' => ['required', 'string', Rule::in($translators)],
        ];
    }
}
