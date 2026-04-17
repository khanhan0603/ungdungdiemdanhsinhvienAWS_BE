<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiemDanh;
use App\Models\ChiTietDiemDanh;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiemDanhController extends Controller
{

    public function luuDiemDanh(Request $request)
    {
        $request->validate([
            'malichthi' => 'required',
        ]);

        // nếu đã có phiên → dùng lại
        $diemDanh = DiemDanh::where('malichthi', $request->malichthi)->first();

        if (!$diemDanh) {
            $diemDanh = DiemDanh::create([
                'madiemdanh' => 'DD' . now()->format('YmdHis') . rand(111, 999),
                'malichthi' => $request->malichthi,
                'soluongdiemdanh' => 0,
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $diemDanh
        ]);
    }


    public function luuCTDiemDanh(Request $request)
    {
        $request->validate([
            'madiemdanh' => 'required',
            'masv' => 'required',
            'tinhtrang' => 'required|in:0,1',
        ]);

        // lấy lớp của ca thi
        $malop = DB::table('diem_danhs as dd')
            ->join('lich_this as lt', 'dd.malichthi', '=', 'lt.malichthi')
            ->where('dd.madiemdanh', $request->madiemdanh)
            ->value('lt.malop');

        if (!$malop) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy ca điểm danh'
            ], 404);
        }

        // kiểm tra sinh viên thuộc lớp
        $hopLe = DB::table('sinh_viens')
            ->where('masv', $request->masv)
            ->where('malop', $malop)
            ->exists();

        if (!$hopLe) {
            return response()->json([
                'status' => false,
                'message' => 'Sinh viên không thuộc lớp của ca thi này',
            ], 403);
        }

        // 🔁 INSERT hoặc UPDATE → ĐIỂM DANH LẠI THOẢI MÁI
        DB::table('chi_tiet_diem_danhs')->updateOrInsert(
            [
                'madiemdanh' => $request->madiemdanh,
                'masv' => $request->masv,
            ],
            [
                'tinhtrang' => $request->tinhtrang,
                'thoigian' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Lưu điểm danh thành công',
        ]);
    }


  public function ketThucDiemDanh(Request $request)
    {
        $request->validate([
            'madiemdanh' => 'required',
        ]);

        $soluong = ChiTietDiemDanh::where('madiemdanh', $request->madiemdanh)
            ->where('tinhtrang', 1)
            ->count();

        DiemDanh::where('madiemdanh', $request->madiemdanh)
            ->update(['soluongdiemdanh' => $soluong]);

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật số lượng điểm danh',
            'data' => ['soluongdiemdanh' => $soluong],
        ]);
    }
    
    public function quanLyDiemDanh(Request $request)
    {
        if (!$request->madiemdanh || !$request->malop) {
            return response()->json([
                'status' => false,
                'message' => 'Thiếu dữ liệu'
            ], 400);
        }

        $data = DB::table('svien_diemdanh')
            ->where('madiemdanh', $request->madiemdanh)
            ->where('malop', $request->malop)
            ->orderBy('hoten')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
