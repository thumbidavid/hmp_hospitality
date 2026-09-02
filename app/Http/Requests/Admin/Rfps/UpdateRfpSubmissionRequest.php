<?php

namespace App\Http\Requests\Admin\Rfps;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRfpSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:new,contacted,quoted,won,lost'],
        ];
    }
}
