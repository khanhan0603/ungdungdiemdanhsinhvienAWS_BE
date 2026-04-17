<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ThongKeTheoNgayExport;
use Maatwebsite\Excel\Facades\Excel;

class ThongKeController extends Controller
{
   

   public function thongKeTheoNgay(Request $request,$email)
    {
        // 1. Validate (cho phép dd/MM/yyyy)
        $request->validate(
            [
                'ngaybatdau'  => 'required',
                'ngayketthuc' => 'required',
            ],
            [
                'ngaybatdau.required'  => 'Vui lòng chọn ngày bắt đầu',
                'ngayketthuc.required' => 'Vui lòng chọn ngày kết thúc',
            ]
        );

        try {
            // 2. Convert dd/MM/yyyy -> Y-m-d
            $ngaybatdau  = Carbon::createFromFormat('d/m/Y', $request->ngaybatdau)->startOfDay();
            $ngayketthuc = Carbon::createFromFormat('d/m/Y', $request->ngayketthuc)->endOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Định dạng ngày không hợp lệ (dd/MM/yyyy)'
            ], 422);
        }

        // 3. Check nghiệp vụ
        if ($ngaybatdau->gt($ngayketthuc)) {
            return response()->json([
                'status' => false,
                'message' => 'Ngày bắt đầu không được lớn hơn ngày kết thúc'
            ], 400);
        }


        // 5. Lấy thông tin admin
        $admin = DB::table('admins')
            ->where('email', $email)
            ->first();

        // 6. Thống kê theo khoảng ngày
        $data = DB::table('lich_this as lt')
        ->join('svien_diemdanh as sv', 'sv.malichthi', '=', 'lt.malichthi')
        ->select(
            'lt.malichthi',
            'lt.monthi',
            DB::raw('DATE(lt.ngaythi) as ngaythi'),
            'lt.giobatdau',
            'lt.gioketthuc',
            'lt.phongthi',

            // tổng số SV trong lớp
            DB::raw('COUNT(sv.masv) as tong_sv'),

            // số SV có mặt
            DB::raw('SUM(CASE WHEN sv.tinhtrang = 1 THEN 1 ELSE 0 END) as soluong_comat'),

            // số SV vắng (NULL = chưa điểm danh)
            DB::raw('SUM(CASE WHEN sv.tinhtrang IS NULL THEN 1 ELSE 0 END) as soluong_vang')
        )
        //->whereDate('lt.ngaythi', $ngaybatdau->toDateString())
        ->whereBetween('lt.ngaythi', [
        $ngaybatdau->toDateString(),
        $ngayketthuc->toDateString()
        ])
        ->groupBy(
            'lt.malichthi',
            'lt.monthi',
            'lt.ngaythi',
            'lt.giobatdau',
            'lt.gioketthuc',
            'lt.phongthi'
        )
        ->get();


        // 7. Trả kết quả
        return response()->json([
            'status' => true,
            'thoi_gian' => [
                'ngaybatdau'  => $ngaybatdau->format('d/m/Y'),
                'ngayketthuc' => $ngayketthuc->format('d/m/Y'),
            ],
            'admin_lap_thong_ke' => [
                'hoten'  => $admin->hoten ?? null,
                'chucvu' => $admin->chucvu ?? null,
                'email'  => $admin->email ?? null
            ],
            'data' => $data
        ]);
    }
    public function thongKeTheoNgayExcel(Request $request, $email)
    {
        // 1. Validate
        $request->validate([
            'ngaybatdau'  => 'required',
            'ngayketthuc' => 'required',
        ]);

        try {
            $ngaybatdau  = Carbon::createFromFormat('d/m/Y', $request->ngaybatdau)->toDateString();
            $ngayketthuc = Carbon::createFromFormat('d/m/Y', $request->ngayketthuc)->toDateString();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Định dạng ngày không hợp lệ (dd/MM/yyyy)'
            ], 422);
        }

        if ($ngaybatdau > $ngayketthuc) {
            return response()->json([
                'status' => false,
                'message' => 'Ngày bắt đầu không được lớn hơn ngày kết thúc'
            ], 400);
        }

        // 2. Lấy admin
        $admin = DB::table('admins')
            ->where('email', $email)
            ->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy admin'
            ], 404);
        }

        // 3. Xuất Excel
        $fileName = 'thong_ke_diem_danh_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new ThongKeTheoNgayExport($ngaybatdau, $ngayketthuc, $admin),
            $fileName
        );
    }

}
