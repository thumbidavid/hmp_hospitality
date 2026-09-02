<?php

namespace App\Http\Requests\Admin\Partners;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'partner_type' => [
                'required',
                'string',
                'in:gds,dmc,tourism_board,association,technology,distribution,media,other'
            ],
            'logo' => ['nullable', 'integer', 'exists:media,id'], // Dynamic Media ID reference
            'website_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
