<?php

// use App\Http\Controllers\AdminController;
// use App\Http\Controllers\GiangVienController;
// use App\Http\Controllers\LopController;
// use App\Http\Controllers\SinhVienController;
// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('giangvien.login');
// })->name('index');

// Route::prefix('admin')->name('admin.')->group(function () {
//    Route::get('/login', function () {
//     return view('admin.login');
//     })->name('login');
//     Route::post('/login', [AdminController::class, 'loginPost'])
//     ->name('loginPost');
//     Route::get('/dashboard', function () {
//         return view('admin.dashboard');
//     })->name('dashboard');

//     // Danh sách giảng viên
//     Route::get('/listgv', [GiangVienController::class, 'listgv'])->name('listgv');
//     // Form import giảng viên
//     Route::get('/import', [GiangVienController::class, 'showImportForm'])
//     ->name('showImportForm');
//     // Xử lý import giảng viên
//     Route::post('/import', [GiangVienController::class, 'importPost'])
//     ->name('importPost');

//     //Import sinh viên
//     Route::get('/importsv', function () {
//         return view('admin.import_sv');
//     })->name('showImportFormSV');
//     Route::post('/importsv', [SinhVienController::class, 'importPost'])
//     ->name('importPostSV');
//     // Danh sách sinh viên
//     Route::get('/listsv', [SinhVienController::class, 'listsv'])
//     ->name('listsv');

//     // Danh sách lớp
//     Route::get('/listlop', [LopController::class, 'listlop'])
//     ->name('listlop');
// });

// Route::prefix('giangvien')->name("giangvien.")->group(function () {
//     // Login giảng viên
//      Route::post('/login', [GiangVienController::class, 'loginPost'])
//     ->name('loginPost');
//    Route::get('/dashboard', function () {
//         return view('giangvien.dashboard');
//     })->name('dashboard');
//     // Quên mật khẩu
//     Route::get('/forgotpassword', [GiangVienController::class, 'forgotPassword'])
//     ->name('forgotPassword');
//     Route::post('/sendresetcode', [GiangVienController::class, 'sendResetCode'])
//     ->name('sendResetCode');
//     // Nhập mã code
//    Route::get('/verifycode', function () {
//         return view('giangvien.verifycode');
//     })->name('verifyCode');
//     // Xử lý mã code
//     Route::post('/verifycode', [GiangVienController::class, 'verifycodePost'])
//     ->name('verifycodePost');
//     // Đặt lại mật khẩu
//     Route::get('/resetpassword', [GiangVienController::class, 'resetPassword'])
//     ->name('resetPassword');
//     Route::post('/reserpassword',[GiangVienController::class,'resetPasswordPost'])
//     ->name('resetPasswordPost');
//  });

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/test-upload', function () {
    Storage::disk('s3')->put('test.txt', 'hello from laravel');
    return 'OK';
});