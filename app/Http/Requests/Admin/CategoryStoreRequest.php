<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'alias' => 'required|string|max:400',
            'note' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'published' => 'boolean',
            'parent_id' => 'required|integer',
            'access' => 'required|integer|min:0',
            'extension' => 'required|string|max:50',
            'language' => 'nullable|string|max:7',
            'metadesc' => 'nullable|string|max:1024',
            'metakey' => 'nullable|string|max:1024'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'alias.required' => 'The alias field is required.',
            'parent_id.required' => 'The parent category is required.',
            'access.required' => 'The access level is required.',
            'extension.required' => 'The extension field is required.',
        ];
    }
}
