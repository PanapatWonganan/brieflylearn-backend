<?php

namespace App\Http\Requests;

class AcceptFriendRequestRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Request ID comes from route parameter
        // Additional validation happens in controller
        return [];
    }
}
