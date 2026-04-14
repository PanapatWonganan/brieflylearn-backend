<?php

namespace App\Http\Requests;

class RegisterRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'full_name' => 'required|string|min:2|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{9,10}$/'],
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
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'full_name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'full_name.string' => 'ชื่อ-นามสกุลต้องเป็นข้อความ',
            'full_name.min' => 'ชื่อ-นามสกุลต้องมีอย่างน้อย 2 ตัวอักษร',
            'full_name.max' => 'ชื่อ-นามสกุลต้องไม่เกิน 255 ตัวอักษร',
            'phone.string' => 'เบอร์โทรศัพท์ต้องเป็นข้อความ',
            'phone.max' => 'เบอร์โทรศัพท์ต้องไม่เกิน 20 ตัวอักษร',
            'phone.regex' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง (ต้องเป็นตัวเลข 9-10 หลัก)',
        ];
    }
}
