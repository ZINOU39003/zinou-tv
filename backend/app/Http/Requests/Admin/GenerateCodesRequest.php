<?php

namespace App\Http\Requests\Admin;

use App\Enums\CodeDuration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GenerateCodesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duration' => ['required', new Enum(CodeDuration::class)],
            'count' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
