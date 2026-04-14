<?php

namespace App\Http\Requests;

class EnrollCourseRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
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
            'course_id.required' => 'กรุณาเลือกคอร์สเรียน',
            'course_id.exists' => 'ไม่พบคอร์สเรียนที่เลือก',
        ];
    }
}
