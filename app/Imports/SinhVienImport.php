<?php

namespace App\Imports;

use App\Models\Lop;
use App\Models\SinhVien;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SinhVienImport implements ToModel, WithHeadingRow
{
    use Importable;

    public int $imported = 0;
    public int $duplicated = 0;
    public int $invalid = 0;

    public array $duplicatedMasv = [];
    public array $errorRows = [];   // dòng lỗi chi tiết

    protected int $currentRow = 1;  // header = dòng 1

    public function model(array $row)
    {
        $this->currentRow++; // bắt đầu từ dòng 2
         // BỎ QUA DÒNG TRỐNG HOÀN TOÀN
        if (
            empty($row['ma_sinh_vien']) &&
            empty($row['ho_ten']) &&
            empty($row['gioi_tinh']) &&
            empty($row['email']) &&
            empty($row['sdt']) &&
            empty($row['ngay_sinh']) &&
            empty($row['ma_lop'])
        ) {
            return null; // không tính là lỗi
        }
        $ma_sv = $row['ma_sinh_vien'] ?? null;

        // Thiếu mã SV
        if (!$ma_sv) {
            $this->invalid++;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => 'Thiếu mã sinh viên'
            ];
            return null;
        }
        // Trùng mã SV
        if (SinhVien::where('masv', $ma_sv)->exists()) {
            $this->duplicated++;
            $this->duplicatedMasv[] = $ma_sv;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => "Trùng mã sinh viên ($ma_sv)"
            ];
            return null;
        }

        // Lớp không tồn tại
        $lop = Lop::where('malop', $row['ma_lop'] ?? null)->first();
        if (!$lop) {
            $this->invalid++;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => 'Lớp không tồn tại'
            ];
            return null;
        }

        // import được dòng nào sẽ thêm dòng đó vào
        $this->imported++;

        return new SinhVien([
            'masv'       => $ma_sv,
            'hoten'      => $row['ho_ten'] ?? null,
            'gioitinh'   => $row['gioi_tinh'] ?? null,
            'email'      => $row['email'] ?? null,
            'sdt'        => $row['sdt'] ?? null,
            'ngaysinh'   => isset($row['ngay_sinh'])
                ? (is_numeric($row['ngay_sinh'])
                    ? Date::excelToDateTimeObject($row['ngay_sinh'])->format('Y-m-d')
                    : Carbon::parse($row['ngay_sinh'])->format('Y-m-d'))
                : null,
            'malop'      => $lop->malop,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
