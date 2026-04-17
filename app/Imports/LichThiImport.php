<?php

namespace App\Imports;

use App\Models\LichThi;
use App\Models\Lop;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LichThiImport implements ToModel, WithHeadingRow
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
            empty($row['ma_lich_thi']) &&
            empty($row['mon_thi']) &&
            empty($row['ngay_thi']) &&
            empty($row['gio_bat_dau']) &&
            empty($row['gio_ket_thuc']) &&
            empty($row['phong_thi']) &&
            empty($row['ma_lop'])
        ) {
            return null; // không tính là lỗi
        }
        $ma_lt = $row['ma_lich_thi'] ?? null;

        // Thiếu mã 
        if (!$ma_lt) {
            $this->invalid++;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => 'Thiếu mã lịch thi'
            ];
            return null;
        }
        // Trùng mã
        if (LichThi::where('malichthi', $ma_lt)->exists()) {
            $this->duplicated++;
            $this->duplicatedMasv[] = $ma_lt;
            $this->errorRows[] = [
                'row' => $this->currentRow,
                'reason' => "Trùng mã lịch thi ($ma_lt)"
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
        
        return new LichThi([
            'malichthi'  => $row['ma_lich_thi'] ?? null,
            'monthi'     => $row['mon_thi'] ?? null,

            //Xử lý cột ngày thi
            'ngaythi'    => isset($row['ngay_thi'])
                ? (is_numeric($row['ngay_thi'])
                    ? Date::excelToDateTimeObject($row['ngay_thi'])->format('Y-m-d')
                    : Carbon::parse($row['ngay_thi'])->format('Y-m-d'))
                : null,

            //Giờ bắt đầu
            'giobatdau'  => $this->convertToTime($row['gio_bat_dau'] ?? null),

            //Giờ kết thúc
            'gioketthuc' => $this->convertToTime($row['gio_ket_thuc'] ?? null),

            'phongthi'   => $row['phong_thi'] ?? null,
            'malop'      => $lop ? $lop->malop : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Chuyển đổi giờ từ Excel sang định dạng HH:MM:SS
     */
    private function convertToTime($value)
    {
        if (!$value) {
            return null;
        }

        // Nếu là dạng số (Excel lưu giờ dưới dạng số thập phân)
        if (is_numeric($value)) {
            $seconds = (int) round($value * 24 * 60 * 60);
            return gmdate('H:i:s', $seconds);
        }

        // Nếu là chuỗi, ví dụ "08:30", "8h30", "8:30 AM"
        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
