<?php

namespace App\Http\Requests\Admin\BuyerTypes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBuyerTypeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $buyerTypeId = $this->route('buyer_type') ? $this->route('buyer_type')->id : null;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:buyer_types,slug,' . $buyerTypeId],
        ];
    }
}
