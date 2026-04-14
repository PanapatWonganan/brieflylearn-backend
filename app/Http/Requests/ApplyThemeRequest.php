<?php

namespace App\Http\Requests;

class ApplyThemeRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'theme_id' => 'required|string|max:50',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'theme_id.required' => 'กรุณาเลือกธีม',
            'theme_id.string' => 'รหัสธีมต้องเป็นข้อความ',
            'theme_id.max' => 'รหัสธีมต้องไม่เกิน 50 ตัวอักษร',
        ];
    }
}
