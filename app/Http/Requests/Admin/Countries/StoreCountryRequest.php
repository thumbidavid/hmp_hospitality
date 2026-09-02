<?php

namespace App\Http\Requests\Admin\Countries;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Handled via route-level authentication middleware
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
            'name' => ['required', 'string', 'max:100'],
            'iso2_code' => ['nullable', 'string', 'size:2', 'unique:countries,iso2_code'],
            'region' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Custom error messages for country validation.
     */
    public function messages(): array
    {
        return [
            'iso2_code.size' => 'The ISO2 code must be exactly 2 characters.',
            'iso2_code.unique' => 'This ISO2 code has already been registered.',
        ];
    }
}
