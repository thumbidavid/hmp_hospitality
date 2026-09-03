<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'string', 'min:8'], // Nullable to preserve unchanged password
            'roles' => ['required', 'string', 'in:admin,staff'],
            'is_active' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'integer', 'exists:media,id'],
        ];
    }
}
