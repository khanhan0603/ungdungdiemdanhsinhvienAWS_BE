<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nganh extends Model
{
    protected $table = 'nganhs';
    protected $primaryKey = 'manganh';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['manganh','tennganh']; // Các cột có thể gán giá trị hàng loạt
}
