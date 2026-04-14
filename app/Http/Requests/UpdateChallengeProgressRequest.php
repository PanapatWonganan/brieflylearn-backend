<?php

namespace App\Http\Requests;

class UpdateChallengeProgressRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'increment' => 'nullable|integer|min:1|max:100',
            'progress_data' => 'nullable|array',
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
            'increment.integer' => 'ค่าความคืบหน้าต้องเป็นตัวเลข',
            'increment.min' => 'ค่าความคืบหน้าต้องไม่น้อยกว่า 1',
            'increment.max' => 'ค่าความคืบหน้าต้องไม่เกิน 100',
            'progress_data.array' => 'ข้อมูลความคืบหน้าต้องเป็นแบบ array',
        ];
    }
}
