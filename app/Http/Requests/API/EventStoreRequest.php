<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'date' => ['required','date'],
            'location' => ['required','string','max:255'],
        ];
    }
}
