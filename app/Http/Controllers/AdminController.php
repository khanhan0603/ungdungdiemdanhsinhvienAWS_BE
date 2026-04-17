<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Http\Requests\LoginRequest_Admin;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Mail\ResetCodeMail;
use App\Models\Admin;
use App\Models\PasswordResset;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    //Nhận email + password; kiểm tra đăng nhập; trả về token nếu đúng, lỗi nếu sai
    public function loginPost(LoginRequest_Admin $request)
    {
        $credentials = $request->only('email', 'password');
        //Auth: Facade xác thực của Laravel, dùng cho login, logout, kiểm tra user
        //guard ('admin_api'): guard này dành riêng cho admin được khai báo trong config/auth.php
        //attemp: tìm admin theo email, so sánh password nhập vào với pasword đã hash trong DB
        if (! $token = Auth::guard('admin_api')->attempt($credentials)) {
            // Trả về json false
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không đúng!'
            ], 401);
        }
        //Đúng thì tạo JWT token, trả về token
        return $this->respondWithToken($token);
    
    }
    //Chuẩn hóa response token: trả về token chuẩn JWT REST API
      protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,//JWT token
            'token_type' => 'bearer',//Loại token (bearer)
            'expires_in' => config('jwt.ttl') * 60, //thời gian hết hạn (giây)
            'admin'        => Auth::guard('admin_api')->user() //Thông tin admin, hàm user () : lấy user từ token
        ]);
    }
     public function logout()
    {
        Auth::guard('admin_api')->logout();

        return response()->json([
            'status' => true,
            'message' => 'Đăng xuất thành công!'
        ], 200);
    }

    public function refresh()
    {
        try {
        $newToken = Auth::refresh();  
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Token không hợp lệ hoặc đã hết hạn!",
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'token'   => $newToken,
            'type'    => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60, // vẫn dùng được
        ], 200);
    }
    //Lấy thông tin từ token
    public function me()
    {
        $admin = auth('admin_api')->user();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Token không hợp lệ!',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => $admin,
        ], 200);
    }
     //Gửi code qua email
  public function sendResetCode(EmailRequest $request)
    {
        $email = $request->email;

        $admin = Admin::where('email', $email)->first();
        if (!$admin) {
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
     public function resetPasswordPost(ResetPasswordRequest $request)
    {
         $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Email không tồn tại'
            ]);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        PasswordResset::where('email', $request->email)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đặt lại mật khẩu thành công'
        ], 200);
    }
    
     // Update admin
    public function updateAdmin(UpdateAdminRequest $request,$admin){
        try {
            $admin=Admin::find($admin);

            // Kiểm tra có tồn tại không
            if(!$admin){
                return response()->json([
                    'status'=>false,
                    'message'=>'Không tìm thấy admin với mã: '.$admin,
                ],404);
            }
             //Cập nhật thông tin (chỉ các field hợp lệ)
             $admin->update($request->validated());
             return response()->json([
                'status'=>true,
                'message'=>'Cập nhật thông tin admin " ' . $admin->hoten . ' " thành công!',
                'data'=>$admin,
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
                'message' => 'Có lỗi xảy ra khi cập nhật admin: ' . $e->getMessage(),
            ], 500);
        }
    }
}


