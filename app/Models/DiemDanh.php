<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DiemDanh extends Model
{
    use Notifiable;
    protected $table = 'diem_danhs';
     protected $primaryKey = 'madiemdanh';
    // Khóa chính không phải là id mặc định của Eloquent nên cần khai báo thêm này nữa
    // Nếu khóa chính là chuỗi (string) thì cần khai báo thêm thuộc tính $keyType
    // Nếu khóa chính không phải là số tự động tăng thì đặt public $incrementing = false;
    public $incrementing = false;
    // $keyType = 'string'; // Nếu khóa chính không phải là số tự động tăng, hãy đặt thành 'string'
    //Là tự động tăng thì đặt $keyType = 'int'
    protected $keyType = 'string';
    public $timestamps = true;// Laravel sẽ tự động set created_at và updated_at
    protected $fillable = [
        'madiemdanh','masv','malichthi','tinhtrang'
    ];

    public function lichthi()
    {
        // malichthi là khóa ngoại trong bảng diem_danhs, malichthi là khóa chính trong bảng lich_this
        return $this->belongsTo(LichThi::class, 'malichthi', 'malichthi');
    }
    public function sinhvien(){
        return $this->belongsToMany(SinhVien::class,'chi_tiet_diem_danhs','masv','madiemdanh')
                    ->withPivot('tinhtrang','thoigian')
                    ->withTimestamps();
    }
}
