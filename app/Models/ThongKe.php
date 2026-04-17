<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ThongKe extends Model
{
    use Notifiable;// thêm use Notifiable nếu cần thông báo
    protected $table = 'thong_kes';
     protected $primaryKey = 'mathongke';
    // Khóa chính không phải là id mặc định của Eloquent nên cần khai báo thêm này nữa
    // Nếu khóa chính là chuỗi (string) thì cần khai báo thêm thuộc tính $keyType
    // Nếu khóa chính không phải là số tự động tăng thì đặt public $incrementing = false;
    public $incrementing = false;
    // $keyType = 'string'; // Nếu khóa chính không phải là số tự động tăng, hãy đặt thành 'string'
    //Là tự động tăng thì đặt $keyType = 'int'
    protected $keyType = 'string';
    public $timestamps = true;// Laravel sẽ tự động set created_at và updated_at
    protected $fillable = [
        'mathongke','soluongcomat','soluongvang','ngaythongke','malichthi','madiemdanh','id_admin'
    ];
    public function lichthi()
    {
        // malichthi là khóa ngoại trong bảng thong_kes, malichthi là khóa chính trong bảng lich_this
        return $this->belongsTo(LichThi::class, 'malichthi', 'malichthi');
    }
    public function diemdanh()
    {
        // madiemdanh là khóa ngoại trong bảng thong_kes, madiemdanh là khóa chính trong bảng diem_danhs
        return $this->belongsTo(DiemDanh::class, 'madiemdanh', 'madiemdanh');
    }
    public function admin()
    {
        // id_admin là khóa ngoại trong bảng thong_kes, id là khóa chính trong bảng admins
        return $this->belongsTo(Admin::class, 'id_admin', 'id');
    }

}
