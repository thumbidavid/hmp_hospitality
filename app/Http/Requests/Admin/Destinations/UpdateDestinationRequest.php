<?php

namespace App\Http\Requests\Admin\Destinations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDestinationRequest extends FormRequest
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
        $destinationId = $this->route('destination') ? $this->route('destination')->id : null;

        return [
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:destinations,slug,' . $destinationId],
            'intro_description' => ['nullable', 'string'],
            'access_connectivity' => ['nullable', 'string'],
            'best_time_to_visit' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:500'],

            // Modified: Expects a numeric Media ID
            'hero_image' => ['nullable', 'integer', 'exists:media,id'],

            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            // Nested highlights repeaters
            'reasons' => ['nullable', 'array'],
            'reasons.*.text' => ['required', 'string', 'max:255'],
            'reasons.*.sort_order' => ['nullable', 'integer'],

            'attractions' => ['nullable', 'array'],
            'attractions.*.text' => ['required', 'string', 'max:255'],
            'attractions.*.sort_order' => ['nullable', 'integer'],

            // Nested documents list (supports existing file_urls or new file_ids)
            'docs' => ['nullable', 'array'],
            'docs.*.id' => ['nullable', 'integer'],
            'docs.*.label' => ['required', 'string', 'max:150'],
            'docs.*.file_id' => ['required_without:docs.*.file_url', 'nullable', 'integer', 'exists:media,id'],
            'docs.*.file_url' => ['required_without:docs.*.file_id', 'nullable', 'string'],
            'docs.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
