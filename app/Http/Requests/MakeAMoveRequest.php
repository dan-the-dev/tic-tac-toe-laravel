<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MakeAMoveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'gameId' => ['required', 'int'],
            'player' => ['required',  Rule::in(['X', 'Y']),],
            'position' => ['required', 'int', 'gte:0', 'lte:8'],
        ];
    }
}
