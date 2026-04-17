<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDiemDanh extends Model
{
     protected $table = 'chi_tiet_diem_danhs';

    protected $primaryKey = null;         // bảng không có khóa chính
    public $incrementing = false;         // không tự tăng
    public $timestamps = true;            // có created_at & updated_at

    protected $fillable = [
        'madiemdanh',
        'masv',
        'tinhtrang',
        'thoigian',
    ];

    // Quan hệ với DiemDanh
    public function diemdanh()
    {
        return $this->belongsTo(DiemDanh::class, 'madiemdanh', 'madiemdanh');
    }

    // Quan hệ với SinhVien
    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masv', 'masv');
    }
}
