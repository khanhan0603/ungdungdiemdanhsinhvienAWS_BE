<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportExcelRequest;
use App\Http\Requests\UpdateSinhVienRequest;
use App\Imports\SinhVienImport;
use App\Models\SinhVien;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;

class SinhVienController extends Controller
{
    // Hiển thị danh sách sinh viên
    public function listsv()
    {
        $sinhviens=SinhVien::all();
        return response()->json($sinhviens);
    }
    // Xử lý import sinh viên từ file Excel
    public function importPostSV(ImportExcelRequest $request)
{
    $import = new SinhVienImport();
    Excel::import($import, $request->file('file'));

    $messages = [];

    if ($import->imported > 0) {
        $messages[] = "Import thành công {$import->imported} sinh viên";
    }

    if ($import->duplicated > 0) {
        $messages[] = "Các mã sinh viên trùng: " . implode(', ', $import->duplicatedMasv);
    }

    if ($import->invalid > 0) {
        $messages[] = "Có {$import->invalid} dòng không hợp lệ";
    }

    return response()->json([
        'status' => true,
        'message' => implode('. ', $messages) . '.',
        'errors' => $import->errorRows   //dòng lỗi chi tiết
    ], 200);
}

    // Cập nhật sinh viên
        public function updateSV(UpdateSinhVienRequest $request, $id)
    {
        try {
            $sinhvien = SinhVien::find($id);

            //Kiểm tra sinh viên có tồn tại không
            if (!$sinhvien) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy sinh viên với mã sinh viên: ' . $id,
                ], 404);
            }

            //Cập nhật thông tin (chỉ các field hợp lệ)
            $sinhvien->update($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thông tin sinh viên "' . $sinhvien->hoten . '" (MSSV: ' . $sinhvien->mssv . ') thành công!',
                'data' => $sinhvien,
            ], 200);

        } catch (QueryException $e) {
            //Lỗi database (ví dụ: trùng khóa, ràng buộc)
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể cập nhật vì dữ liệu trùng lặp hoặc vi phạm ràng buộc cơ sở dữ liệu.',
                ], 400);
            }

            return response()->json([
                'status' => false,
                'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage(),
            ], 500);

        } catch (\Exception $e) {
            // Lỗi không xác định
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật sinh viên: ' . $e->getMessage(),
            ], 500);
        }
    }
// Xóa sinh viên
    public function deleteSV($id)
    {
        try {
            $sinhvien = SinhVien::find($id);

            // Kiểm tra sinh viên có tồn tại không
            if (!$sinhvien) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy sinh viên với ID: ' . $id,
                ], 404);
            }

            // Thực hiện xóa
            $sinhvien->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa sinh viên "' . $sinhvien->hoten . '" (MSSV: ' . $sinhvien->mssv . ') thành công!',
            ], 200);

        } catch (QueryException $e) {
            // Lỗi ràng buộc khóa ngoại
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa sinh viên vì đang được liên kết với dữ liệu khác (ví dụ: bảng điểm hoặc lớp học).',
                ], 400);
            }

            // Lỗi truy vấn khác
            return response()->json([
                'status' => false,
                'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage(),
            ], 500);

        } catch (\Exception $e) {
            // Lỗi không xác định
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
    // Lọc sinh viên theo lớp
    public function filterSVByLop($malop){
        $sinhviens=SinhVien::where('malop',$malop)->get();
        return response()->json($sinhviens);
    }
    // Lọc sinh viên theo ngành, qua bảng lớp
    public function filterSVByNganh($manganh){
        $sinhviens=SinhVien::whereHas('lop', function($query) use ($manganh){
            $query->where('manganh',$manganh);
        })->get();
        return response()->json($sinhviens);
    }

}

