<?php
namespace App\Exports;

use App\Models\ReportSPPD;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SPTFinish implements FromView, ShouldAutoSize, WithStyles
{
  public function styles(Worksheet $sheet)
  {
    $header = ['font' => ['bold' => true],  'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]];
    return [
      // Style the first row as bold text.
      1    => $header,
      2    => $header,
      3    => $header,
      5    => $header,
      6    => $header,
      7    => $header,
      'A5:AN45' => ['borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ]
        ],
      ]
    ];
  }

  public function view(): View
  {
    $date = Carbon::now();

    $startOfYear = ($date->copy()->startOfYear())->isoFormat('D MMMM Y');
    $endOfYear   = ($date->copy()->endOfYear())->isoFormat('D MMMM Y');

    return view('Exports.sptFinish', [
      'data' => ReportSPPD::all(),
      'startDate' => $startOfYear,
      'endDate' => $endOfYear,
    ]);
  }
}