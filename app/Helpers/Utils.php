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
      $file->dbPath = 'images/' . $subFolder;
      $file->path = public_path('storage/images/') . $subFolder;
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
      $results->path = $file->path;


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
}