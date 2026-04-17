<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportExcelLichThiRequest extends FormRequest
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
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'malichthi' => 'string|max:191',
            'monthi' => 'required|string|max:191',
            'ngaythi' => 'required|date_format:d/m/Y|after_or_equal:today',
            'giobatdau' => 'required|date_format:H:i',
            'gioketthuc' => 'required|date_format:H:i|after:giobatdau',
            'phongthi' => 'required|string|max:191',
            'malop' => 'required|exists:lops,malop',
        ];
    }
    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file để import',
            'file.file' => 'File không hợp lệ',
            'file.mimes' => 'Chỉ chấp nhận các định dạng file: xlsx, xls, csv',

           
            'malichthi.string' => 'Mã lịch thi phải là chuỗi ký tự',
            'malichthi.max' => 'Mã lịch thi không được vượt quá 191 ký tự.',

            'monthi.required' => 'Môn thi là bắt buộc.',
            'monthi.string' => 'Môn thi phải là chuỗi ký tự.',
            'monthi.max' => 'Môn thi không được vượt quá 191 ký tự.',

            'ngaythi.required' => 'Ngày thi là bắt buộc.',
            'ngaythi.date_format' => 'Ngày thi phải có định dạng dd/mm/yyyy.',
            'ngaythi.after_or_equal' => 'Ngày thi phải lớn hơn hoặc bằng ngày hiện tại.',

            'giobatdau.required' => 'Giờ bắt đầu là bắt buộc.',
            'giobatdau.date_format' => 'Giờ bắt đầu phải có định dạng HH:MM.',

            'gioketthuc.required' => 'Giờ kết thúc là bắt buộc.',
            'gioketthuc.date_format' => 'Giờ kết thúc phải có định dạng HH:MM.',
            'gioketthuc.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',

            'phongthi.required' => 'Phòng thi là bắt buộc.',
            'phongthi.string' => 'Phòng thi phải là chuỗi ký tự.',
            'phongthi.max' => 'Phòng thi không được vượt quá 191 ký tự.',

            'malop.required' => 'Mã lớp là bắt buộc.',
            'malop.exists' => 'Mã lớp không tồn tại trong hệ thống.',
        ];
    }
}
