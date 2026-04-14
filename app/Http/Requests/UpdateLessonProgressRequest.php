<?php

namespace App\Http\Requests;

class UpdateLessonProgressRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'is_completed' => 'sometimes|boolean',
            'watch_time' => 'nullable|integer|min:0',
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
            'is_completed.boolean' => 'สถานะการเรียนต้องเป็น true หรือ false',
            'watch_time.integer' => 'เวลาที่ดูต้องเป็นตัวเลข',
            'watch_time.min' => 'เวลาที่ดูต้องไม่น้อยกว่า 0',
        ];
    }
}
