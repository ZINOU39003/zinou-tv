<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ActivateCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|regex:/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'android_version' => 'nullable|string|max:50',
            'app_version' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'The code format must be ABCD-1234-EFGH-5678.',
        ];
    }
}
