<?php

namespace App\Http\Requests\Admin\Properties;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'portfolio_category_id' => ['required', 'exists:portfolio_categories,id'],
            'destination_id' => ['required', 'exists:destinations,id'],
            'country_id' => ['required', 'exists:countries,id'],

            'name' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:properties,slug'],
            'tagline' => ['nullable', 'string', 'max:300'],
            'overview' => ['nullable', 'string'],

            'accommodation_details' => ['nullable', 'string'],
            'meetings_facilities_details' => ['nullable', 'string'],
            'dining_leisure_details' => ['nullable', 'string'],
            'sustainability_details' => ['nullable', 'string'],

            'city' => ['nullable', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'number_of_rooms' => ['nullable', 'integer', 'min:0'],
            'max_event_capacity' => ['nullable', 'integer', 'min:0'],

            'website_url' => ['nullable', 'url', 'max:500'],
            'featured_image' => ['nullable', 'integer', 'exists:media,id'], // Media library reference

            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            // Pivot selectors (Settings & Amenities)
            'settings' => ['nullable', 'array'],
            'settings.*' => ['integer', 'exists:settings,id'],

            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],

            // Nested key experiences repeaters
            'key_experiences' => ['nullable', 'array'],
            'key_experiences.*.text' => ['required', 'string', 'max:255'],
            'key_experiences.*.sort_order' => ['nullable', 'integer'],

            // Nested documents repeaters
            'docs' => ['nullable', 'array'],
            'docs.*.label' => ['required', 'string', 'max:150'],
            'docs.*.file_id' => ['required', 'integer', 'exists:media,id'],
            'docs.*.sort_order' => ['nullable', 'integer'],

            // Nested image gallery repeaters
            'gallery' => ['nullable', 'array'],
            'gallery.*.media_id' => ['required', 'integer', 'exists:media,id'],
            'gallery.*.caption' => ['nullable', 'string', 'max:255'],
            'gallery.*.is_cover' => ['nullable', 'boolean'],
            'gallery.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
