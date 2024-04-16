<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CurrencySearchRequest extends FormRequest
{
    /**
     * Authorizes every request by default.
     *
     * @return bool Always returns true, indicating that every request is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Provides the validation rules that are applied to the request data.
     * Fields are optional individually but at least one must be provided.
     *
     * @return array The array of validation rules.
     */
    public function rules(): array
    {
        return [
            'code' => 'nullable|string|size:3',
            'code_list' => 'nullable|array',
            'code_list.*' => 'string|size:3',
            'number' => 'nullable|integer',
            'number_list' => 'nullable|array',
            'number_list.*' => 'integer',
        ];
    }



    /**
     * Custom validation logic to ensure that at least one of the fields is provided.
     *
     * @param Validator $validator The Validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            $data = $this->all();
            // Verifica se pelo menos um campo está preenchido
            if (empty($data['code']) && empty($data['code_list']) && empty($data['number']) && empty($data['number_list'])) {
                $validator->errors()->add('fields', 'At least one field (code, code_list, number, or number_list) must be provided.');
            }
        });
    }

    /**
     * Defines custom error messages for the validation rules.
     *
     * @return array An associative array where keys are rule identifiers and values are error messages.
     */
    public function messages(): array
    {
        return [
            'code.string' => 'The currency code must be a string.',
            'code.size' => 'The currency code must be exactly 3 characters.',
            'code_list.array' => 'The code_list must be an array.',
            'code_list.*.string' => 'Each item in the code list must be a string.',
            'code_list.*.size' => 'Each currency code in the list must be exactly 3 characters.',
            'number.integer' => 'The currency number must be an integer.',
            'number_list.array' => 'The number_list must be an array.',
            'number_list.*.integer' => 'Each item in the number list must be an integer.',
        ];
    }

    /**
     * Converts all input parameters to an array of strings.
     *
     * @return array
     */
    public function normalizedInput(): array
    {
        $normalized = [];
        $data = $this->all();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $normalized = array_merge($normalized, array_map('strval', $value));
            } else {
                $normalized[] = strval($value);
            }
        }

        return $normalized;
    }
}
