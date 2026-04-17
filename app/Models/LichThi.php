<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class LichThi extends Model
{
    // Dùng notifiable nếu muốn dùng tính năng thông báo (notifications)
    use Notifiable;

    protected $table = 'lich_this';
    protected $primaryKey = 'malichthi';
    public $incrementing = false; // Nếu khóa chính không phải là số tự động tăng, đặt thành false
    // $keyType = 'string'; // Nếu khóa chính không phải là số tự động tăng, hãy đặt thành 'string'
    //Là tự động tăng thì đặt $keyType = 'int'
    protected $keyType = 'string';

    protected $fillable = [
        'malichthi','monthi','ngaythi','giobatdau', 'gioketthuc','phongthi', 'malop'
    ];
    
    // Ép ngaythi thành Carbon
    protected $casts = [
        'ngaythi' => 'date',
    ];

    // Thêm field ảo
    protected $appends = ['ngaythi_format'];

    public function getNgaythiFormatAttribute()
    {
        return $this->ngaythi
            ? $this->ngaythi->format('d/m/Y')
            : null;
    }
    
    public function lop()
    {
        // malop là khóa ngoại trong bảng lops, malop là khóa chính trong bảng lich_this
        return $this->belongsTo(Lop::class, 'malop', 'malop');
    }
    public function giangViens()
    {
        return $this->belongsToMany(GiangVien::class,'phan_cong_lich_this','malichthi','magv')
                    ->withPivot('vaitro')
                    ->withTimestamps();// Bảng phân công lịch thi
    }
}
