<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['required', 'string', 'in:admin,staff'],
            'is_active' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'integer', 'exists:media,id'], // Integrates with FileUploader media ID
        ];
    }
}
