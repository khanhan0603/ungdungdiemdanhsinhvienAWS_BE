<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SinhVien extends Model
{
    use Notifiable;
    protected $table = 'sinh_viens';
     protected $primaryKey = 'masv'; //masv
    // Khóa chính không phải là id mặc định của Eloquent nên cần khai báo thêm này nữa
    // Nếu khóa chính là chuỗi (string) thì cần khai báo thêm thuộc tính $keyType
    // Nếu khóa chính không phải là số tự động tăng thì đặt public $incrementing = false;
    public $incrementing = false;
    // $keyType = 'string'; // Nếu khóa chính không phải là số tự động tăng, hãy đặt thành 'string'
    //Là tự động tăng thì đặt $keyType = 'int'
    protected $keyType = 'string';
    public $timestamps = true;// Laravel sẽ tự động set created_at và updated_at
    protected $fillable = [
        'masv','hoten','gioitinh','email','sdt', 'ngaysinh','malop'
    ];
     // Ép ngaysinh thành Carbon
    protected $casts = [
        'ngaysinh' => 'date',
    ];

    // Thêm field ảo
    protected $appends = ['ngaysinh_format'];

    public function getNgaysinhFormatAttribute()
    {
        return $this->ngaysinh
            ? $this->ngaysinh->format('d/m/Y')
            : null;
    }

    // quan hệ với lop (nếu có)
    public function lop()
    {
        // malop là khóa ngoại trong bảng sinh_viens, malop là khóa chính trong bảng lops
        return $this->belongsTo(Lop::class, 'malop', 'malop');
    }
    public function diemdanh() {
       return $this->belongsToMany(DiemDanh::class,'chi_tiet_diem_danhs','masv','madiemdanh')
                    ->withPivot('tinhtrang','thoigian')
                    ->withTimestamps();
    }
    
}
