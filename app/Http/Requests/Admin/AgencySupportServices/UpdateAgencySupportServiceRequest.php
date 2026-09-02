<?php

namespace App\Http\Requests\Admin\AgencySupportServices;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgencySupportServiceRequest extends FormRequest
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
        $serviceId = $this->route('agency_support_service') ? $this->route('agency_support_service')->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:agency_support_services,slug,' . $serviceId],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
