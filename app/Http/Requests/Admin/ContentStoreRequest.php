<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContentStoreRequest extends FormRequest
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
            'introtext' => 'nullable|string',
            'fulltext' => 'nullable|string',
            'state' => 'required|integer|in:0,1,2,-2',
            'catid' => 'required|integer|exists:vjprf_categories,id',
            'created_by_alias' => 'nullable|string|max:255',
            'publish_up' => 'nullable|date',
            'publish_down' => 'nullable|date|after:publish_up',
            'metakey' => 'nullable|string',
            'metadesc' => 'nullable|string',
            'access' => 'required|integer|min:0',
            'featured' => 'boolean',
            'language' => 'nullable|string|max:7',
            'note' => 'nullable|string|max:255'
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
            'state.required' => 'The state field is required.',
            'catid.required' => 'The category is required.',
            'catid.exists' => 'The selected category does not exist.',
            'access.required' => 'The access level is required.',
            'publish_down.after' => 'The publish down date must be after the publish up date.',
        ];
    }
}
