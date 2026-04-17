<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hoten' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'chucvu'=>'required|string|max:191',

        ];
    }
    public function messages(): array
    {
        return [
            'hoten.required' => 'Họ tên là bắt buộc.',
            'hoten.string' => 'Họ tên phải là chuỗi ký tự.',
            'hoten.max' => 'Họ tên không được vượt quá 191 ký tự.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 191 ký tự.',

            'chucvu.required'=>'Chức vụ là bắt buộc',
            'chucvu.string'=>'Chức vụ phải là chuỗi ký tự',
            'chucvu.max'=>'Chức vụ không được vượt quá 191 ký tự',
        ];
    }


}
