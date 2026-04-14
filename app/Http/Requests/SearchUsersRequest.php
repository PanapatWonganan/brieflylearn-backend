<?php

namespace App\Http\Requests;

class SearchUsersRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2|max:50',
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
            'query.required' => 'กรุณากรอกคำค้นหา',
            'query.string' => 'คำค้นหาต้องเป็นข้อความ',
            'query.min' => 'กรุณากรอกคำค้นหาอย่างน้อย 2 ตัวอักษร',
            'query.max' => 'คำค้นหาต้องไม่เกิน 50 ตัวอักษร',
        ];
    }
}
