<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportExcelRequest extends FormRequest
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
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ];
    }
    public function messages()
    {
        return [
            'file.required' => 'Vui lòng chọn file để import',
            'file.file' => 'File không hợp lệ',
            'file.mimes' => 'Chỉ chấp nhận các định dạng file: xlsx, xls, csv'
        ];
    }
}
