<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateLichThiRequest;
use App\Imports\LichThiImport;
use App\Models\LichThi;
use App\Models\SinhVien;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LichThiController extends Controller
{
    // Danh sách lich thi
     public function listslt()
     {
        $lichthi=LichThi::all();
        return response()->json([
            'status' => true,
            'data' => $lichthi,
        ], 200);
    }

    // Xử lý import lich thi từ file Excel
     public function importLT(Request $request)
    {
        
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ],
            [
            'file.required' => 'Vui lòng chọn file để import',
            'file.file' => 'File không hợp lệ',
            'file.mimes' => 'Chỉ chấp nhận các định dạng file: xlsx, xls, csv',

            ]);     
        $import = new LichThiImport();
        Excel::import($import, $request->file('file'));

        $messages = [];

        if ($import->imported > 0) {
            $messages[] = "Import thành công {$import->imported} lịch thi";
        }

        if ($import->duplicated > 0) {
            $messages[] = "Các mã lịch thi trùng: " . implode(', ', $import->duplicatedMasv);
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

    public function updateLT(UpdateLichThiRequest $request, $malt)
    {
        try {
            $lichthi = LichThi::find($malt);

            if (!$lichthi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lịch thi với mã lịch thi: ' . $malt,
                ], 404);
            }

            $data = $request->validated();

            // Convert ngày thi dd/MM/yyyy -> Y-m-d
            if (!empty($data['ngaythi'])) {
                $data['ngaythi'] = Carbon::createFromFormat(
                    'd/m/Y',
                    $data['ngaythi']
                )->format('Y-m-d');
            }

            // Convert giờ AM/PM -> 24h
            if (!empty($data['giobatdau'])) {
                $data['giobatdau'] = Carbon::createFromFormat(
                    'h:i A',
                    $data['giobatdau']
                )->format('H:i:s');
            }

            if (!empty($data['gioketthuc'])) {
                $data['gioketthuc'] = Carbon::createFromFormat(
                    'h:i A',
                    $data['gioketthuc']
                )->format('H:i:s');
            }

            $lichthi->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thông tin lịch thi thành công!',
                'data' => $lichthi,
            ], 200);

        } catch (QueryException $e) {
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
            return response()->json([
                'status' => false,
                'message' => 'Ngày thi không đúng định dạng (dd/MM/yyyy)',
            ], 422);
        }
    }

    // Xóa lịch thi
     public function deleteLT($malt)
    {
        try {
            $lichthi = LichThi::find($malt);

            // Kiểm tra lịch thi có tồn tại không
            if (!$lichthi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy lịch thi với mã lịch thi: ' . $malt,
                ], 404);
            }

            // Thực hiện xóa
            $lichthi->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa lịch thi thành công!',
            ], 200);

        } catch (QueryException $e) {
            // Lỗi ràng buộc khóa ngoại
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa lịch thi vì đang được liên kết với dữ liệu khác.',
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
    public function phanCongLichThi(Request $request, $malichthi)
    {
        $request->validate([
            'magv' => 'required|exists:giang_viens,magv',
             'vaitro' => 'nullable|string',
        ]);

        $lichthi = LichThi::where('malichthi', $malichthi)->firstOrFail();

        $magv = $request->magv;
        $vaitro = $request->vaitro;

       // Kiểm tra: vai trò này đã có người chưa?
        if (!empty($vaitro)) {
            $roleUsed = $lichthi->giangviens()
                                ->wherePivot('vaitro', $vaitro)
                                ->exists();

            if ($roleUsed) {
                return response()->json([
                    'status' => false,
                    'message' => "Vai trò $vaitro đã được phân công cho lịch thi $malichthi!",
                ], 400);
            }
        }

        // Kiểm tra trùng ca thi của giảng viên
        $ngay = $lichthi->ngaythi;
        $start = $lichthi->giobatdau;
        $end = $lichthi->gioketthuc;

        $trung = LichThi::whereHas('giangviens', function ($q) use ($magv) {
                        $q->where('giang_viens.magv', $magv);
                    })
                    ->where('malichthi', '!=', $malichthi)
                    ->whereDate('ngaythi', $ngay)
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('giobatdau', [$start, $end])
                        ->orWhereBetween('gioketthuc', [$start, $end])
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('giobatdau', '<=', $start)
                            ->where('gioketthuc', '>=', $end);
                        });
                    })
                    ->exists();

        if ($trung) {
            return response()->json([
                'status' => false,
                'message' => "Giảng viên $magv bị trùng lịch thi!",
            ], 400);
        }

        // Gán giảng viên với vai trò
        $lichthi->giangviens()->syncWithoutDetaching([
            $magv => ['vaitro' => $vaitro]
        ]);

        return response()->json([
            'status' => true,
            'message' => "Phân công $magv vào vai trò $vaitro thành công!",
        ], 200);
    }

    // Danh sách phân công lịch thi đã phân công
   public function listPhanCongLichThi(){
    $phancong = LichThi::with('giangviens')->get();

    return response()->json([
        'status' => true,
        'data' => $phancong,
    ], 200);
   }
    // Hủy phân công lịch thi
    public function huyPhanCong($malichthi, $magv){
            
        $lichthi = LichThi::where('malichthi', $malichthi)->firstOrFail();

        // Kiểm tra giảng viên có trong lịch thi không
        $exists = $lichthi->giangviens()
            ->where('giang_viens.magv', $magv)
            ->exists();

        if (!$exists) {
            return response()->json([
                'status'  => false,
                'message' => "Giảng viên $magv không được phân công trong lịch thi $malichthi",
            ], 404);
        }

        // Hủy phân công giảng viên đó
        $lichthi->giangviens()->detach($magv);

        return response()->json([
            'status'  => true,
            'message' => "Đã hủy phân công giảng viên $magv khỏi lịch thi $malichthi",
        ], 200);
    }
    // Cập nhật phân công lịch thi
    public function capNhatPhanCong(Request $request, $malichthi){
            $request->validate([
            'magv'   => 'required|exists:giang_viens,magv',
            'vaitro' => 'required|string',
        ]);

        $lichthi = LichThi::where('malichthi', $malichthi)->firstOrFail();
        $magv    = $request->magv;
        $vaitro  = $request->vaitro;

        // CHECK TRÙNG CA THI
        $ngay  = $lichthi->ngaythi;
        $start = $lichthi->giobatdau;
        $end   = $lichthi->gioketthuc;

        $trung = LichThi::whereHas('giangviens', function ($q) use ($magv) {
                $q->where('giang_viens.magv', $magv);
            })
            ->where('malichthi', '!=', $malichthi)
            ->whereDate('ngaythi', $ngay)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('giobatdau', [$start, $end])
                ->orWhereBetween('gioketthuc', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('giobatdau', '<=', $start)
                        ->where('gioketthuc', '>=', $end);
                });
            })
            ->exists();

        if ($trung) {
            return response()->json([
                'status'  => false,
                'message' => "Giảng viên $magv bị trùng ca thi!",
            ], 400);
        }

        //GỠ GIẢNG VIÊN CŨ CỦA VAI TRÒ
        $lichthi->giangviens()
            ->wherePivot('vaitro', $vaitro)
            ->detach();
        
            // GÁN / CHUYỂN VAI TRÒ
        $lichthi->giangviens()->syncWithoutDetaching([
            $magv => ['vaitro' => $vaitro]
        ]);

        return response()->json([
            'status'  => true,
            'message' => "Cập nhật phân công lịch thi $malichthi thành công!",
        ], 200);
    }
//    Lấy danh sách sinh viên trong ca thi từ mã lớp
    public function danhSachSVTrongCaThi($malichthi){
        $lichthi = LichThi::where('malichthi', $malichthi)->firstOrFail();
        $malop = $lichthi->malop;

        $sinhviens = SinhVien::where('malop', $malop)->get();


        return response()->json([
            'status' => true,
            'data' => $sinhviens,
        ], 200);
    }
    
}   
