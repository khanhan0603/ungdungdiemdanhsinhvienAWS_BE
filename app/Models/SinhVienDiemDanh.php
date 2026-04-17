<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SinhVienDiemDanh extends Model
{
     protected $table = 'svien_diemdanh';
    public $timestamps = false;

    protected $fillable = [
        'masv',
        'hoten',
        'malop',
        'madiemdanh',
        'soluongdiemdanh',
        'tinhtrang'
    ];
}
