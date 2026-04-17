<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject as ContractsJWTSubject;

class GiangVien extends Authenticatable implements ContractsJWTSubject
{
    // Dùng notifiable nếu muốn dùng tính năng thông báo (notifications)
    use Notifiable;

    protected $table = 'giang_viens';
    protected $primaryKey = 'magv'; //magv
    // Khóa chính không phải là id mặc định của Eloquent nên cần khai báo thêm này nữa
    // Nếu khóa chính là chuỗi (string) thì cần khai báo thêm thuộc tính $keyType
    // Nếu khóa chính không phải là số tự động tăng thì đặt public $incrementing = false;
    public $incrementing = false;
    // $keyType = 'string'; // Nếu khóa chính không phải là số tự động tăng, hãy đặt thành 'string'
    //Là tự động tăng thì đặt $keyType = 'int'
    protected $keyType = 'string';

    protected $fillable = [
        'magv','hoten','email','sdt', 'password', 'manganh'
    ];

    protected $hidden = ['password'];

    // quan hệ với ngành (nếu có)
    public function nganh()
    {
        // manganh là khóa ngoại trong bảng giang_viens, manganh là khóa chính trong bảng nganhs
        return $this->belongsTo(Nganh::class, 'manganh', 'manganh');
    }
    public function lichThis()
    {
        return $this->belongsToMany(LichThi::class,'phan_cong_lich_this','magv','malichthi')
                    ->withPivot('vaitro')//vai trò trong phân công lịch thi
                    ->withTimestamps();// Bảng phân công lịch thi
    }
     // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
