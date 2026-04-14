<?php

namespace App\Http\Requests;

class SendFriendRequestRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'friend_email' => 'required|email|exists:users,email',
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
            'friend_email.required' => 'กรุณากระอีเมลของเพื่อน',
            'friend_email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'friend_email.exists' => 'ไม่พบผู้ใช้ที่มีอีเมลนี้',
        ];
    }
}
