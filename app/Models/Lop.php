<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    protected $table = 'lops';
    protected $primaryKey = 'malop';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['malop', 'manganh']; // Các cột có thể gán giá trị hàng loạt

     // Quan hệ tới bảng ngành
    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'manganh', 'manganh');
    }
}
