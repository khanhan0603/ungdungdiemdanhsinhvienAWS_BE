<?php

namespace App\Imports;

use App\Models\GiangVien;
use App\Models\Nganh;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class GiangVienImport implements ToModel,WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    /**
     * Hàm này chạy cho từng hàng (row) trong file Excel
     */
    public int $imported = 0;
    public int $duplicated = 0;//check trùng mã gv
    public int $duplicatedEmail = 0;//check trùng email
     public int $duplicatedPhone = 0;//check trùng phone
    public int $invalid = 0;

    public array $duplicatedMasv = [];//mảng mã gv bị trùng
    public array $errorRows = [];   // dòng lỗi chi tiết
    
    public array $duplicatedEmails = [];

    public array $duplicatedPhones = [];

    protected int $currentRow = 1;  // header = dòng 1

    public function model(array $row)
    {
        $this->currentRow++; // bắt đầu từ dòng 2
         // BỎ QUA DÒNG TRỐNG HOÀN TOÀN
        if (
            empty($row['ma_giang_vien']) &&
            empty($row['ho_ten']) &&
            empty($row['email']) &&
            empty($row['sdt']) &&
            empty($row['mat_khau']) &&
            empty($row['ma_nganh'])
        ) {
            return null; // không tính là lỗi
        }
        $ma_gv = $row['ma_giang_vien'] ?? null;

        // Thiếu mã GV
        if (!$ma_gv) {
            $this->invalid++;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => 'Thiếu mã giảng viên'
            ];
            return null;
        }
        // Trùng mã SV
        if (GiangVien::where('magv', $ma_gv)->exists()) {
            $this->duplicated++;
            $this->duplicatedMasv[] = $ma_gv;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => "Trùng mã giảng viên ($ma_gv)"
            ];
            return null;
        }

        //TRÙNG EMAIL
        $email = $row['email'] ?? null;
        if ($email && GiangVien::where('email', $email)->exists()) {
            $this->duplicatedEmail++;
            $this->duplicatedEmails[] = $email;

            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => "Trùng email ($email)"
            ];
            return null;
        }

        //TRÙNG PHONE
        $phone = $row['sdt'] ?? null;
        if ($phone && GiangVien::where('sdt', $phone)->exists()) {
            $this->duplicatedPhone++;
            $this->duplicatedPhones[] = $phone;

            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => "Trùng số điện thoại ($phone)"
            ];
            return null;
        }

        // Ngành không tồn tại
        $nganh = Nganh::where('manganh', $row['ma_nganh'] ?? null)->first();
        if (!$nganh) {
            $this->invalid++;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => 'Ngành không tồn tại'
            ];
            return null;
        }

        // import được dòng nào sẽ thêm dòng đó vào
        $this->imported++;

        return new GiangVien([
            'magv'       => $row['ma_giang_vien'] ?? null,   // hoặc 'magv' nếu header là "magv"
            'hoten'      => $row['ho_ten'] ?? null,
            'email'      => $row['email'] ?? null,
            'sdt'        => $row['sdt'] ?? null,
            'password'   => Hash::make($row['mat_khau'] ?? '123456'),
            'manganh'    => $nganh ? $nganh->manganh : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
    /**
     * Bắt đầu đọc từ hàng số 2 (vì hàng 1 là tiêu đề)
     */
    public function headingRow(): int
    {
        return 1;
    }
}
