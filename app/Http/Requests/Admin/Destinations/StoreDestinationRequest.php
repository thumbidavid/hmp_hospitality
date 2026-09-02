<?php

namespace App\Http\Requests\Admin\Destinations;

use Illuminate\Foundation\Http\FormRequest;

class StoreDestinationRequest extends FormRequest
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
        return [
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:destinations,slug'],
            'intro_description' => ['nullable', 'string'],
            'access_connectivity' => ['nullable', 'string'],
            'best_time_to_visit' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:500'],

            // Modified: Expects a numeric Media ID uploaded asynchronously
            'hero_image' => ['nullable', 'integer', 'exists:media,id'],

            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            // Nested content highlights repeaters
            'reasons' => ['nullable', 'array'],
            'reasons.*.text' => ['required', 'string', 'max:255'],
            'reasons.*.sort_order' => ['nullable', 'integer'],

            'attractions' => ['nullable', 'array'],
            'attractions.*.text' => ['required', 'string', 'max:255'],
            'attractions.*.sort_order' => ['nullable', 'integer'],

            // Nested documents list (Modified: Expects file_id instead of file)
            'docs' => ['nullable', 'array'],
            'docs.*.label' => ['required', 'string', 'max:150'],
            'docs.*.file_id' => ['required', 'integer', 'exists:media,id'],
            'docs.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
