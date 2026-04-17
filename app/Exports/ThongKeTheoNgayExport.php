<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ThongKeTheoNgayExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    ShouldAutoSize
{
    protected $ngaybatdau;
    protected $ngayketthuc;
    protected $admin;

    public function __construct($ngaybatdau, $ngayketthuc, $admin)
    {
        $this->ngaybatdau  = $ngaybatdau;
        $this->ngayketthuc = $ngayketthuc;
        $this->admin = $admin;
    }

    /* =======================
        DỮ LIỆU
    ======================= */
    public function collection()
    {
        return DB::table('lich_this as lt')
            ->join('svien_diemdanh as sv', 'sv.malichthi', '=', 'lt.malichthi')
            ->select(
                'lt.malichthi',
                'lt.monthi',
                'lt.ngaythi',
                'lt.giobatdau',
                'lt.gioketthuc',
                'lt.phongthi',
                DB::raw('COUNT(sv.masv) as tong_sv'),
                DB::raw('SUM(CASE WHEN sv.tinhtrang = 1 THEN 1 ELSE 0 END) as comat'),
                DB::raw('SUM(CASE WHEN sv.tinhtrang IS NULL THEN 1 ELSE 0 END) as vang')
            )
            ->whereBetween('lt.ngaythi', [
                $this->ngaybatdau,
                $this->ngayketthuc
            ])
            ->groupBy(
                'lt.malichthi',
                'lt.monthi',
                'lt.ngaythi',
                'lt.giobatdau',
                'lt.gioketthuc',
                'lt.phongthi'
            )
            ->orderBy('lt.ngaythi')
            ->get();
    }

    /* =======================
        HEADER CỘT
    ======================= */
    public function headings(): array
    {
        return [
            'STT',
            'Mã lịch thi',
            'Môn thi',
            'Ngày thi',
            'Giờ bắt đầu',
            'Giờ kết thúc',
            'Phòng thi',
            'Tổng SV',
            'Có mặt',
            'Vắng'
        ];
    }

    /* =======================
        MAP DỮ LIỆU
    ======================= */
    public function map($row): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $row->malichthi,
            $row->monthi,
            date('d/m/Y', strtotime($row->ngaythi)),
            $row->giobatdau,
            $row->gioketthuc,
            $row->phongthi,
            $row->tong_sv,
            $row->comat,
            $row->vang,
        ];
    }

    /* =======================
        GHI THÔNG TIN ADMIN
    ======================= */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Chèn 3 dòng trên cùng
                $event->sheet->insertNewRowBefore(1, 3);

                $event->sheet->setCellValue('A1', 'THỐNG KÊ ĐIỂM DANH');
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $event->sheet->setCellValue('A2', 'Người lập: ' . ($this->admin->hoten ?? ''));
                $event->sheet->setCellValue('A3', 'Chức vụ: ' . ($this->admin->chucvu ?? ''));
            }
        ];
    }
}