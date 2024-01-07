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
  protected $jenis_dinas;
  protected $tahun;

  public function __construct($jenis_dinas, $tahun)
  {
    $this->jenis_dinas = $jenis_dinas;
    $this->tahun = $tahun;
  }

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
    $date = Carbon::parse('01-01-' . $this->tahun);

    $startOfYear = ($date->copy())->isoFormat('D MMMM Y');
    $endOfYear   = ($date->copy()->endOfYear())->isoFormat('D MMMM Y');

    $q = ReportSPPD::orderBy('id', 'DESC');

    if ($this->jenis_dinas == 'Dalam Daerah') {
			$q = $q->whereRaw("upper(lok_asal) != 'KABUPATEN KERINCI'");
		} else if ($this->jenis_dinas == 'Luar Daerah') {
			$q = $q->whereRaw("upper(lok_asal) = 'KABUPATEN KERINCI'");
		}

    $data = $q->get();

    return view('Exports.sptFinish', [
      'data' => $data,
      'startDate' => $startOfYear,
      'endDate' => $endOfYear,
    ]);
  }
}