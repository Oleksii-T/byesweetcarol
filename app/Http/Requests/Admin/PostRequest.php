<?php

namespace App\Http\Requests\Admin;

use App\Enums\PostStatus;
use App\Enums\PostTCStyle;
use App\Rules\RichImageInputRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
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
        $model = $this->route('post');
        $reqNull = $model ? 'nullable' : 'required';
        $modelId = $model ? "$model->id,id" : 'NULL,NULL';

        return [
            'parent_id' => ['nullable', 'exists:posts,id'],
            'is_hidden' => ['nullable', 'boolean'],
            'human_written' => ['nullable', 'boolean'],
            'sub_title' => ['nullable', 'string', 'max:5000'],
            'title' => ['required', 'string', 'max:255'],
            'links_title' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['required', 'string', 'max:255'],
            'meta_description' => ['required', 'string', 'max:255'],
            'intro' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', "unique:posts,slug,$modelId,deleted_at,NULL"],
            'thumbnail' => [new RichImageInputRule(false)],
            'status' => ['required', Rule::in(PostStatus::values())],
            'tc_style' => ['required', Rule::in(PostTCStyle::values())],
            'category_id' => ['nullable', 'exists:categories,id'],
            'game_id' => ['nullable'],
            'author_id' => ['required', 'exists:authors,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'exists:tags,id'],
        ];
    }
}
