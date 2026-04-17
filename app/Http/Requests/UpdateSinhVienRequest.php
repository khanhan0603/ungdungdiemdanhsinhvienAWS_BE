<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSinhVienRequest extends FormRequest
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
            'gioitinh' => 'required|in:Nam,Nữ,Khác',
            'ngaysinh' => 'required|date',
            'email' => 'required|email|max:255',
            'malop' => 'required|exists:lops,malop', 
        ];
    }
    public function messages(): array
    {
        return [
            'hoten.required' => 'Họ tên là bắt buộc.',
            'hoten.string' => 'Họ tên phải là chuỗi ký tự.',
            'hoten.max' => 'Họ tên không được vượt quá 255 ký tự.',

            'gioitinh.required' => 'Giới tính là bắt buộc.',
            'gioitinh.in' => 'Giới tính không hợp lệ.',

            'ngaysinh.required' => 'Ngày sinh là bắt buộc.',
            'ngaysinh.date' => 'Ngày sinh không hợp lệ.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',

            'malop.required' => 'Mã lớp là bắt buộc.',
            'malop.exists' => 'Mã lớp không tồn tại.',
        ];
    }


}
