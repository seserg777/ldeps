<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gte:price_min',
            'manufacturer' => 'nullable|array',
            'manufacturer.*' => 'integer|exists:vjprf_jshopping_manufacturers,manufacturer_id',
            'category' => 'nullable|string',
            'sort' => 'nullable|string|in:price_asc,price_desc,name_asc,name_desc,newest',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'price_min.numeric' => 'Минимальная цена должна быть числом.',
            'price_max.numeric' => 'Максимальная цена должна быть числом.',
            'price_max.gte' => 'Максимальная цена должна быть больше или равна минимальной.',
            'manufacturer.array' => 'Производитель должен быть массивом.',
            'manufacturer.*.integer' => 'ID производителя должен быть числом.',
            'manufacturer.*.exists' => 'Выбранный производитель не существует.',
            'sort.in' => 'Недопустимый тип сортировки.',
            'per_page.integer' => 'Количество товаров на странице должно быть числом.',
            'per_page.min' => 'Минимум 1 товар на странице.',
            'per_page.max' => 'Максимум 100 товаров на странице.',
        ];
    }

    /**
     * Get the validated data with defaults.
     *
     * @return array
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'price_min' => null,
            'price_max' => null,
            'manufacturer' => [],
            'category' => null,
            'sort' => 'newest',
            'per_page' => 12,
        ], $validated);
    }
}
