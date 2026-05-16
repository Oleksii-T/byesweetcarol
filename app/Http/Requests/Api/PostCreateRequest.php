<?php

namespace App\Http\Requests\Api;

use App\Rules\NotRussian;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostCreateRequest extends FormRequest
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
        return [
            // 'parent_id' => ['nullable', 'exists:posts,id'],
            // 'is_hidden' => ['nullable', 'boolean'],
            // 'human_written' => ['nullable', 'boolean'],
            'id' => ['nullable', 'string'],
            'title' => ['required', 'string', 'max:255', new NotRussian],
            // 'links_title' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['required', 'string', 'max:255'],
            'meta_description' => ['required', 'string', 'max:255'],
            'blocks' => ['nullable', 'required_without:body', 'string', 'json'],
            'body' => ['nullable', 'required_without:blocks', 'string'],
            // 'intro' => ['nullable', 'string'],
            // 'slug' => ['required', 'string', 'max:255', 'unique:posts,slug'],
            'thumbnail' => ['nullable', 'string'],
            // 'status' => ['required', Rule::in(PostStatus::values())],
            // 'tc_style' => ['required', Rule::in(PostTCStyle::values())],
            // 'category_id' => ['nullable', 'exists:categories,id'],
            // 'game_id' => ['nullable'],
            // 'author_id' => ['required', 'exists:authors,id'],
            'tags' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'publish' => ['nullable', 'boolean'],
            'author' => ['nullable', 'string'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        \Log::debug('N8N Webhook Validation Failed', [
            'errors' => $validator->errors()->toArray(),
            'input_data' => $this->all(),
            'url' => $this->url(),
            'method' => $this->method(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
