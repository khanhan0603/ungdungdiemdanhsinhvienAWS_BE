<?php

use App\Http\Controllers\GiangVienController;
use App\Http\Controllers\LopController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DiemDanhController;
use App\Http\Controllers\LichThiController;
use App\Http\Controllers\NganhController;
use App\Http\Controllers\ThongKeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

// Login admin
Route::prefix('auth')->group(function () {

    // Login không cần token
    Route::post('adminlogin', [AdminController::class, 'loginPost']);//api/auth/adminlogout
     Route::post('admin-send-reset-code', [AdminController::class, 'sendResetCode']);
    Route::post('admin-verify-reset-code', [AdminController::class, 'verifycodePost']);
    Route::post('admin-reset-password', [AdminController::class, 'resetPasswordPost']);

    // Các route cần token
    Route::middleware('auth:admin_api')->group(function () {
        Route::post('adminlogout', [AdminController::class, 'logout']);//api/auth/adminlogout
        Route::post('adminrefresh', [AdminController::class, 'refresh']);
        Route::get('adminme', [AdminController::class, 'me']);
    });

});
//Update admin
Route::post('/adminupdate/{admin}', [AdminController::class, 'updateAdmin']);

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('send-reset-code', [GiangVienController::class, 'sendResetCode']);
    Route::post('verify-reset-code', [GiangVienController::class, 'verifycodePost']);
    Route::post('reset-password', [GiangVienController::class, 'resetPasswordPost']);
    Route::post('login', [GiangVienController::class, 'login']);//api/auth/login
    Route::post('logout', [GiangVienController::class, 'logout']);//api/auth/logout
    Route::post('refresh', [GiangVienController::class, 'refresh']);//api/auth/refresh
    Route::get('me', [GiangVienController::class, 'me']);

});
// Danh sách giảng viên
Route::get('/lists',[GiangVienController::class,'listgv']);
// Danh sách sinh viên
Route::get('/listsv', [SinhVienController::class, 'listsv']);
// Danh sách lớp
Route::get('/listlop', [LopController::class, 'listlop']);
// Danh sách ngành
Route::get('/listnganh', [NganhController::class, 'listnganh']);
// Danh sách lịch thi
Route::get('/listslt', [LichThiController::class, 'listslt']);

// Import giảng viên
Route::post('/gvimport', [GiangVienController::class, 'importPost']);
// Update giảng viên
Route::post('/gvupdate/{magv}', [GiangVienController::class, 'updateGV']);
// Xóa giảng viên
Route::post('/gvdelete/{magv}', [GiangVienController::class, 'deleteGV']);
// Lấy giảng viên theo email
Route::get('/gv/{email}', [GiangVienController::class, 'getGV']);
// Lấy họ tên giảng viên để phân công
Route::get('/gvname', [GiangVienController::class, 'getName']);
// Lọc giảng viên theo ngành
Route::get('/gvnganh/{manganh}', [GiangVienController::class, 'filterGVByNganh']);
// Hiển thị phân công giảng viên theo email
Route::get('/phanconggv/{email}', [GiangVienController::class, 'listPhanCongGiangVien']);

// Import sinh viên
Route::post('/svimport', [SinhVienController::class, 'importPostSV']);
// Update sinh viên
Route::post('/svupdate/{masv}', [SinhVienController::class, 'updateSV']);
// Xóa sinh viên
Route::post('/svdelete/{masv}', [SinhVienController::class, 'deleteSV']);
// Lọc sinh viên theo lớp
Route::get('/svlop/{malop}', [SinhVienController::class, 'filterSVByLop']);
// Lọc sinh viên theo ngành
Route::get('/svnganh/{manganh}', [SinhVienController::class, 'filterSVByNganh']);

// Import lich thi
Route::post('/ltimport', [LichThiController::class, 'importLT']);
// Update lich thi
Route::post('/ltupdate/{malichthi}', [LichThiController::class, 'updateLT']);
// Xóa lich thi
Route::post('/ltdelete/{malichthi}', [LichThiController::class, 'deleteLT']);
//Phân công lịch thi
Route::post('/phanconglt/{malichthi}', [LichThiController::class, 'phanCongLichThi']);
// Danh sách phân công lịch thi
Route::get('/listspc', [LichThiController::class, 'listPhanCongLichThi']);  
// Hủy phân công lịch thi
Route::post('/huypc/{malichthi}/giangvien/{magv}', [LichThiController::class, 'huyPhanCongGiangVien']);
// Cap nhat phan công lịch thi
Route::post('/capnhatpc/{malichthi}', [LichThiController::class, 'capNhatPhanCong']);

// Danh sách sinh viên trong ca thi
Route::get('/svtrongcathi/{malichthi}', [LichThiController::class, 'danhSachSVTrongCaThi']);

// Bắt đầu điểm danh sinh viên
Route::post('/luudiemdanh', [DiemDanhController::class, 'luuDiemDanh']);
// Lưu chi tiết điểm danh sinh viên
Route::post('/luuctdiemdanh', [DiemDanhController::class, 'luuCTDiemDanh']);
//Kết thúc điểm danh
Route::post('/ketthucdiemdanh',[DiemDanhController::class,'ketThucDiemDanh']);

//Danh sách điểm danh và lịch thi được phân công cho giảng viên
Route::get('/danhsachdiemdanh/{email}', [GiangVienController::class, 'danhSachDiemDanh']);

// quản lý điểm danh
Route::post('/quanlydiemdanh', [DiemDanhController::class, 'quanLyDiemDanh']);

//Thông kê theo ngày 
Route::post('/thongketheongay/{email}', [ThongKeController::class, 'thongKeTheoNgay']);
//Xuất file thống kê theo ngày
Route::get('/thongketheongayexport/{email}', [ThongKeController::class, 'thongKeTheoNgayExcel']);

//Route upload ảnh sinh viên lên S3 và lấy pre-signed URL
Route::post('/get-multi-upload-url', [UploadController::class, 'getMultiUploadUrl']);
//Route lấy danh sách sinh viên kèm trạng thái ảnh
Route::get('/students/image-status', [UploadController::class, 'getStudentsWithImageStatus']);
