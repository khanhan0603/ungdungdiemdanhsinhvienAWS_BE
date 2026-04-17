<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLichThiRequest extends FormRequest
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
            'monthi'     => 'required|string|max:191',
            'ngaythi'    => ['required', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],

            // HH:MM AM/PM
            'giobatdau'  => 'required|date_format:h:i A',
            'gioketthuc' => 'required|date_format:h:i A',

            'phongthi'   => 'required|string|max:191',
            'malop'      => 'required|exists:lops,malop',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // Check ngày thi
            if ($this->ngaythi) {
                try {
                    $ngayThi = Carbon::createFromFormat('d/m/Y', $this->ngaythi);

                    if ($ngayThi->lt(Carbon::today())) {
                        $validator->errors()->add(
                            'ngaythi',
                            'Ngày thi phải lớn hơn hoặc bằng ngày hiện tại.'
                        );
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add(
                        'ngaythi',
                        'Ngày thi không đúng định dạng dd/MM/yyyy.'
                    );
                }
            }

            // Check giờ bắt đầu < giờ kết thúc
            if ($this->giobatdau && $this->gioketthuc) {
                try {
                    $gioBatDau  = Carbon::createFromFormat('h:i A', $this->giobatdau);
                    $gioKetThuc = Carbon::createFromFormat('h:i A', $this->gioketthuc);

                    if ($gioKetThuc->lte($gioBatDau)) {
                        $validator->errors()->add(
                            'gioketthuc',
                            'Giờ kết thúc phải sau giờ bắt đầu.'
                        );
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add(
                        'giobatdau',
                        'Giờ thi không đúng định dạng HH:MM AM/PM.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'monthi.required' => 'Môn thi là bắt buộc.',
            'monthi.string'   => 'Môn thi phải là chuỗi ký tự.',
            'monthi.max'      => 'Môn thi không được vượt quá 191 ký tự.',

            'ngaythi.required' => 'Ngày thi là bắt buộc.',
            'ngaythi.regex'    => 'Ngày thi phải có định dạng dd/MM/yyyy.',

           'giobatdau.required'    => 'Giờ bắt đầu là bắt buộc.',
            'giobatdau.date_format' => 'Giờ bắt đầu phải có định dạng HH:MM AM/PM.',

            'gioketthuc.required'    => 'Giờ kết thúc là bắt buộc.',
            'gioketthuc.date_format' => 'Giờ kết thúc phải có định dạng HH:MM AM/PM.',

            'phongthi.required' => 'Phòng thi là bắt buộc.',
            'phongthi.string'   => 'Phòng thi phải là chuỗi ký tự.',
            'phongthi.max'      => 'Phòng thi không được vượt quá 191 ký tự.',

            'malop.required' => 'Mã lớp là bắt buộc.',
            'malop.exists'   => 'Mã lớp không tồn tại trong hệ thống.',
        ];
    }
}
