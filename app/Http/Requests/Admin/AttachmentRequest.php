<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
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
        $model = $this->route('attachment');

        return [
            'original_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255', "unique:attachments,name,$model->id"],
            'alt' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'new_file' => ['required'],
            'new_file.file' => ['nullable', 'image'],
        ];
    }
}
