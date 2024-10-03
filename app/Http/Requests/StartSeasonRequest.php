<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartSeasonRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'team_ids' => 'required|array',
            'team_ids.*' => 'exists:teams,id',
            'season_id' => 'required|integer|exists:seasons,id',
        ];
    }
}
