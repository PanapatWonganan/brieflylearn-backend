<?php

namespace App\Http\Requests;

class PlantSeedRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'custom_name' => 'nullable|string|max:255',
            'position' => 'nullable|array',
            'position.x' => 'nullable|integer|min:0|max:10',
            'position.y' => 'nullable|integer|min:0|max:10'
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
            'custom_name.max' => 'ชื่อพืชต้องไม่เกิน 255 ตัวอักษร',
            'position.array' => 'ตำแหน่งต้องเป็นข้อมูลแบบ array',
            'position.x.integer' => 'ตำแหน่ง X ต้องเป็นตัวเลข',
            'position.x.min' => 'ตำแหน่ง X ต้องไม่น้อยกว่า 0',
            'position.x.max' => 'ตำแหน่ง X ต้องไม่เกิน 10',
            'position.y.integer' => 'ตำแหน่ง Y ต้องเป็นตัวเลข',
            'position.y.min' => 'ตำแหน่ง Y ต้องไม่น้อยกว่า 0',
            'position.y.max' => 'ตำแหน่ง Y ต้องไม่เกิน 10',
        ];
    }
}
