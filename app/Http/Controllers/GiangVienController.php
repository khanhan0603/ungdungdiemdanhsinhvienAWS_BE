<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Http\Requests\ImportExcelRequest;
use App\Http\Requests\LoginRequest_GV;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateGiangVienRequest;
use Illuminate\Http\Request;
use App\Mail\ResetCodeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GiangVienImport;
use App\Models\GiangVien;
use App\Models\DiemDanh;
use App\Models\PasswordResset;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class GiangVienController extends \Illuminate\Routing\Controller
{
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login']]);//tất cả các function trong controller đều yêu cầu JWT Token, trừ login
        $this->middleware('auth:api', [
        'except' => [
            'login', 
            'listgv',
            'updateGV',
            'deleteGV',
            'filterGVByNganh',
            'importPost',
            'sendResetCode',
            'verifycodePost',
            'resetPasswordPost',
            'danhSachDiemDanh'
            ]
        ]);
    }
     /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest_GV $request)
    {
       $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không đúng!'
            ], 401);
        }

        return $this->respondWithToken($token);
    }
      /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user'        => Auth::guard('api')->user()
        ]);
    }
    
    //Đăng xuất phiên làm việc
    public function logout(){
        Auth::guard('api')->logout();
        return response()->json([
            'status'=>true,
            'message'=>"Đăng xuất thành công!",
        ],200);
    }

    //refresh token
    public function refresh(){
        try {
        $newToken = Auth::refresh();  //CHUẨN CHO LARAVEL 11/12
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Token không hợp lệ hoặc đã hết hạn!",
            ], 401);
        }

        return response()->json([
            'status'  => true,
            //token cu
            'old_token'=>request()->bearerToken(),//bearerToken() lấy token từ header Authorization
            //token moi
            'token'   => $newToken,
        ], 200);
    }

   public function me()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Token không hợp lệ!',
            ], 401);
        }


        $thongtinuser=DB::table('giang_viens as gv')
                    -> join('nganhs as ng','gv.manganh','=','ng.manganh')
                    ->where('gv.magv',$user->magv)
                    ->select('gv.magv','gv.hoten','gv.email','gv.sdt','ng.tennganh')
                    ->get()
                    ;
        return response()->json([
            'status' => true,
            'data' => $thongtinuser,
        ], 200);
    }

    // Danh sách giảng viên
     public function listgv()
    {
        $giangviens = GiangVien::with('nganh')->get();

        $data = $giangviens->map(function ($gv) {
            return [
                'magv'       => $gv->magv,
                'hoten'      => $gv->hoten,
                'email'      => $gv->email,
                'sdt'        => $gv->sdt,
                'created_at' => $gv->created_at,
                'updated_at' => $gv->updated_at,
                //key manganh nhưng value là tên ngành
                'manganh'    => $gv->nganh ? $gv->nganh->tennganh : null
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    //import Excel giảng viên
     public function importPost(ImportExcelRequest $request)
    {
        $import = new GiangVienImport();
        Excel::import($import, $request->file('file'));

        $messages = [];

        if ($import->imported > 0) {
            $messages[] = "Import thành công {$import->imported} giảng viên";
        }

        if ($import->duplicated > 0) {
            $messages[] = "Các mã giảng viên trùng: " . implode(', ', $import->duplicatedMasv);
        }

        if ($import->duplicatedEmail > 0) {
            $messages[] = "Email trùng: " . implode(', ', $import->duplicatedEmails);
        }

        if($import->duplicatedPhone > 0){
            $messages[]="Số điện thoại trùng: ". implode(', ', $import->duplicatedPhones);
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

    // Lấy giảng viên theo mã giảng viên, lấy tên ngành từ mã nganh
    public function getGV($email){
            $giangvien = GiangVien::with('nganh')->where('email', $email)->first();

        if ($giangvien) {
            return response()->json([
                'status' => true,
                'data' => [
                    'magv'      => $giangvien->magv,
                    'tengv'     => $giangvien->tengv,
                    'email'     => $giangvien->email,
                    'manganh'   => $giangvien->manganh,
                    'tennganh'  => $giangvien->nganh->tennganh ?? null
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy giảng viên với email: '.$email
            ], 404);
        }
    }
  
    //Gửi code qua email
    public function sendResetCode(EmailRequest $request)
    {
        $email = $request->email;

        $gv = GiangVien::where('email', $email)->first();
        if (!$gv) {
            return response()->json([
                'status' => false,
                'message' => 'Email không tồn tại trong hệ thống'
            ], 404);
        }

        $code = rand(100000, 999999);

        // Xoá code cũ (nếu có)
        PasswordResset::where('email', $email)->delete();

        // Lưu code mới
        PasswordResset::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($email)->send(new ResetCodeMail($code));

        return response()->json([
            'status' => true,
            'message' => 'Mã xác nhận đã được gửi qua email',
            'data' => [
                'email' => $email,
                'expires_at' => Carbon::now()->addMinutes(5)->toDateTimeString(),
            ]
        ], 200);
    }
    // Xử lý mã code
    public function verifycodePost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6'
        ]);

        $record = PasswordResset::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Mã xác nhận không đúng.',
            ], 400);
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            $record->delete();

            return response()->json([
                'status' => false,
                'message' => 'Mã xác nhận đã hết hạn.',
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'Xác minh thành công. Vui lòng đặt lại mật khẩu.',
            'data' => [
                'email' => $request->email
            ]
        ], 200);
    }
    // public function resetPassword(){
    //     return view('giangvien.resetpassword');
    // }
    public function resetPasswordPost(ResetPasswordRequest $request)
    {
         $gv = GiangVien::where('email', $request->email)->first();

        if (!$gv) {
            return response()->json([
                'status' => false,
                'message' => 'Email không tồn tại'
            ]);
        }

        $gv->password = Hash::make($request->password);
        $gv->save();

        PasswordResset::where('email', $request->email)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đặt lại mật khẩu thành công'
        ], 200);
    }
    // Update giảng viên
    public function updateGV(UpdateGiangVienRequest $request,$magv){
        try {
            $giangvien=GiangVien::find($magv);

            // Kiểm tra giảng viên có tồn tại không
            if(!$giangvien){
                return response()->json([
                    'status'=>false,
                    'message'=>'Không tìm thấy giảng viên với mã giảng viên: '.$magv,
                ],404);
            }
             //Cập nhật thông tin (chỉ các field hợp lệ)
             $giangvien->update($request->validated());
             return response()->json([
                'status'=>true,
                'message'=>'Cập nhật thông tin giảng viên "' . $giangvien->hoten . '" (MGV: ' . $giangvien->magv . ') thành công!',
                'data'=>$giangvien,
             ],200);
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

        }
        catch(\Exception $e){
            // Lỗi không xác định
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật giảng viên: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Xóa giảng viên
     public function deleteGV($magv)
    {
        try {
            $giangvien = GiangVien::find($magv);

            // Kiểm tra giảng viên có tồn tại không
            if (!$giangvien) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy giảng viên với mã giảng viên: ' . $magv,
                ], 404);
            }

            // Thực hiện xóa
            $giangvien->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa giảng viên "' . $giangvien->hoten . '" (MGV: ' . $giangvien->magv . ') thành công!',
            ], 200);

        } catch (QueryException $e) {
            // Lỗi ràng buộc khóa ngoại
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa giảng viên vì đang được liên kết với dữ liệu khác.',
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
    // Lấy họ tên theo id
    public function getName(){
        return GiangVien::select('magv','hoten')->orderBy('hoten','asc')->get();
    }
    // Lọc giảng viên theo ngành
    public function filterGVByNganh($manganh){
        $giangviens=GiangVien::where('manganh',$manganh)->get();
        return response()->json($giangviens);
    }
    // Hiển thị phân công giảng viên theo email
    public function listPhanCongGiangVien($email){
        $giangvien = GiangVien::where('email', $email)->first();
        if (!$giangvien) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy giảng viên với email: ' . $email
            ], 404);
        }
        $phancong = $giangvien->lichThis; // Sử dụng quan hệ để lấy phân công giảng viên
        return response()->json([
            'status' => true,
            'data' => $phancong
        ], 200);
    }

    public function danhSachDiemDanh($email)
    {
        $lichThis = DB::table('giang_viens as gv')
            ->join('phan_cong_lich_this as pc', 'gv.magv', '=', 'pc.magv')
            ->join('lich_this as l', 'pc.malichthi', '=', 'l.malichthi')
            ->where('gv.email', $email)
            ->select(
                'l.malichthi',
                'l.monthi',
                'l.ngaythi',
                'l.giobatdau',
                'l.gioketthuc',
                'l.malop',
                'pc.vaitro'
            )
            ->get();

        $result = [];

        foreach ($lichThis as $lichThi) {
            $diemDanhs = DiemDanh::where('malichthi', $lichThi->malichthi)->get();

            foreach ($diemDanhs as $diemDanh) {
                $result[] = [
                    'madiemdanh'      => $diemDanh->madiemdanh,
                    'soluongdiemdanh' => $diemDanh->soluongdiemdanh,
                    'malichthi'       => $lichThi->malichthi,
                    'monthi'          => $lichThi->monthi,
                    'ngaythi'         => $lichThi->ngaythi,
                    'giobatdau'       => $lichThi->giobatdau,
                    'gioketthuc'      => $lichThi->gioketthuc,
                    'malop'           => $lichThi->malop,
                    'vaitro'          => $lichThi->vaitro,
                ];
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách điểm danh theo email giảng viên thành công!',
            'data'    => $result,
        ], 200);
    }


}
