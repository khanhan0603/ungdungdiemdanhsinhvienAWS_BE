<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiangVienRequest extends FormRequest
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
            'hoten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sdt'=>'required|max:10',
            'manganh' => 'required|exists:nganhs,manganh', 

        ];
    }
    public function messages(): array
    {
        return [
            'hoten.required' => 'Họ tên là bắt buộc.',
            'hoten.string' => 'Họ tên phải là chuỗi ký tự.',
            'hoten.max' => 'Họ tên không được vượt quá 255 ký tự.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',

            'sdt.required'=>'SDT là bắt buộc',
            'sdt.max'=>'SDT không vượt quá 10 ký tự',

            'manganh.required' => 'Mã ngành là bắt buộc.',
            'manganh.exists' => 'Mã ngành không tồn tại.',
        ];
    }


}
