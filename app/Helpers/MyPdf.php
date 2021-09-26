<?php
namespace App\Helpers;

use setasign\Fpdi\Tcpdf\Fpdi;

class MyPdf extends Fpdi {

  private $customFooterText = Array();
  private $size = Array();
  private $kode = "";

  /**
   * @param string $customFooterText
   */
  public function setCustomFooterText($customFooterText, $size, $kode)
  {
    $this->customFooterText = $customFooterText;
    $this->size = $size;
    $this->kode = $kode;
  }

  public function Footer()
  {
     // Position at 15 mm from bottom
     $this->SetY(-20);
     // Set font
     $this->SetFont('helvetica', 'I', 7);

     $style = array(
      'border' => 1,
      'vpadding' => 'auto',
      'hpadding' => 'auto',
      'fgcolor' => array(0,0,0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
    );
      // QRCODE,L : QR-CODE Low error correction
     $barccod =  $this->write2DBarcode($this->kode, 'QRCODE,M', 180, $this->size[1] - 40, 15, 15, $style, 'N');
      
      //$pdf->write2DBarcode($isiKode, 'QRCODE,L', 170, 260, 15, 15, $style, 'N');
     foreach($this->customFooterText as $footer){
      $this->Cell(185, 10, $footer, 0, false, 'R', 0, '', 0, false, 'T', 'M');
      $this->Ln(4);
     }
     $this->Cell(195, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

  }

}