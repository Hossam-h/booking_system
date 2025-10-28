<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string','max:255'],
            'description' => ['nullable','string'],
            'date' => ['sometimes','required','date'],
            'location' => ['sometimes','required','string','max:255'],
        ];
    }
}
