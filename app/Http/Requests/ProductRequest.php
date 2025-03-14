<?php

// filepath: app/Http/Requests/ProductRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => ['required', Rule::in(['available', 'out_of_stock'])],
            'category_id' => 'required|exists:categories,id',
            'images' => 'sometimes|array',
            'images.*.image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*.is_primary' => 'sometimes|boolean',
        ];

       
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = collect($rules)->map(function ($rule, $field) {
                if (!in_array($field, ['images', 'images.*.image', 'images.*.is_primary'])) {
                    return str_replace('required', 'sometimes', $rule);
                }
                return $rule;
            })->toArray();
        }

        return $rules;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422));
    }
}