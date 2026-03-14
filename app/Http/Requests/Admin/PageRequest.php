<?php

namespace App\Http\Requests\Admin;

use App\Enums\PageStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
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
        $model = $this->route('page');

        if ($model) {
            // update
            $rules = [
                'meta_title' => ['nullable', 'string', 'max:70'],
                'meta_description' => ['nullable', 'string', 'max:255'],
                'title' => ['required', 'string', 'max:70'],
            ];
            if ($model->notDynamic()) {
                return $rules;
            }

            return $rules + [
                'link' => ['required', 'string', 'max:255'],
                'status' => ['required', Rule::in(array_keys(PageStatus::getEditables()))],
                'content' => ['required', 'string', 'max:50000'],
            ];
        }

        //create
        return [
            'title' => ['required', 'string', 'max:70'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(PageStatus::getEditables()))],
            'content' => ['required', 'string', 'max:50000'],
        ];
    }
}
