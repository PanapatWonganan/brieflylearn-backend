<?php

namespace App\Http\Requests;

class WaterPlantRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // No request body validation needed - validation happens in controller
        // The plant ID comes from route parameter
        return [];
    }
}
