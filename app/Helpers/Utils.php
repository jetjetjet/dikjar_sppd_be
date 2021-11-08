<?php
namespace App\Helpers;

use Carbon\Carbon;
use Image;
use File;
use Exception;

use App\Models\File as FileModel;

class Utils
{
  public static function fileUpload($inputs, $subFolder)
  {
    $file = new \StdClass;
    try {
      $file = isset($inputs['file']) ? $inputs['file'] : null;
      
      $file->path = storage_path('app/public/storage/files/') . $subFolder;
      $file->newName = time()."_".$file->getClientOriginalName();
      $file->originalName = explode('.',$file->getClientOriginalName())[0];
      $file->move($file->path ,$file->newName);

      if (!File::isDirectory($file->path)){
        File::makeDirectory($file->path);
      }
      $file = self::saveFile($file);
    } catch (Exception $e){
      // supress
      $file = null;
    }
    return $file;
  }

  public static function imageUpload($inputs, $subFolder)
  {
    $file = new \StdClass;
    try {
      if($inputs['file'] == 'null' || $inputs['file'] == null) throw new Exception();
      $file =  $inputs['file'];
      
      // $file->path = storage_path('public/storage/images/') . $subFolder;
      $file->dbPath = '/storage/images/' . $subFolder;
      $file->path = 'storage/images/' . $subFolder;
      $file->newName = time()."_".$file->getClientOriginalName();
      $file->originalName = explode('.',$file->getClientOriginalName())[0];
      $file->ext = explode('.',$file->getClientOriginalName())[1];
      if (!File::isDirectory($file->path)){
        File::makeDirectory($file->path);
      }
      
      Image::make($file)->save($file->path . '/' . $file->newName);
      $file->nPath =  public_path('storage/images/') . $subFolder;
      
      $results = new \stdClass();
      $results->id = self::saveFile($file);
      $results->path = $file->dbPath . "/" . $file->newName;


      // //buat folder tumbnail
      // $tumbPath = $file->path . 'thumbnail/';
      // if (!File::isDirectory($tumbPath)) {
      //   File::makeDirectory($tumbPath);
      // }

      // $img = Image::make($file);
      // $img->resize(160, 160)->save($tumbPath . $file->newName);
    } catch (Exception $e){
      // supress
      $results = null;
    }
    return $results;
  }

  public static function saveFile($file)
  {
    $file = FileModel::create([
      'file_name' => $file->newName,
      'original_name' =>  $file->originalName,
      'file_path' => $file->dbPath,
      'ext' => $file->ext,
    ]);
    
    return $file->id;
  }

  public static function rupiahTeks($nominal)
  {
    $angka = Array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
    $kata = Array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan');
    $tingkat = Array('','Ribu','Juta','Milyar','Triliun');

    try {
      $nominalString = (string)$nominal;
      $nominalLength = \strlen($nominalString);
      if( $nominalLength > 15 ){
        $kalimat = 'Nominal tidak diketahui/diluar batas';
        return $kalimat;
      }
  
      for( $i = 1; $i <= $nominalLength; $i++) {
        $angka[$i] = substr($nominalString, -($i), 1);
      }
  
      $i = 1;
      $j = 0;
      $kalimat = '';
      while ($i <= $nominalLength) {
        $subKalimat = '';
        $kata1 = '';
        $kata2 = '';
        $kata3 = '';
  
        if ($angka[$i+2] != "0") {
          if ($angka[$i+2] == "1") {
            $kata1 = "Seratus";
          } else {
            $kata1 = $kata[$angka[$i+2]] . " Ratus";
          }
        }
        
  
        //Puluhan atau belasan
        if ($angka[$i+1] != "0") { 
          if ($angka[$i+1] == "1") { 
            if ($angka[$i] == "0") { 
              $kata2 = "Sepuluh";
            } else if ($angka[$i] == "1") { 
              $kata2 = "Sebelas";
            } else {
              $kata2 = $kata[$angka[$i]] . " Belas";
            }
          } else {
            $kata2 = $kata[$angka[$i + 1]] . " Puluh";
          }
        }
  
        //Satuan
        if ($angka[$i] != "0") {
          if ($angka[$i+1] != "1") {
            $kata3 = $kata[$angka[$i]];
          }
        }
  
        // pengujian angka apakah tidak nol semua, lalu ditambahkan tingkat 
        if (($angka[$i] != "0") || ($angka[$i+1] != "0") || ($angka[$i+2] != "0")) {
          $subKalimat = $kata1 . " " . $kata2 . " " . $kata3 ." " . $tingkat[$j] ." ";
        }
  
        // gabungkan variabe sub kaLimat (untuk Satu blok 3 angka) ke variabel kaLimat
        $kalimat = $subKalimat . $kalimat;
        $i = $i + 3;
        $j = $j + 1;
      }
  
      if (($angka[5] == "0") && ($angka[6] == "0")) {
        $kalimat = \str_replace("Satu Ribu", "Seribu", $kalimat);
      }
     
    } catch(\Exception $e) {
      // dd($e);
		}
    
    return $kalimat . "Rupiah";
  }
}