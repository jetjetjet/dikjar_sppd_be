<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs;
  protected $responses = array('state_code' => 202, 'success' => false, 'messages' => array(), 'data' => Array());

  public function responJson($results)
	{
		// $data = [
		// 	'breadcrumb' => $this->breadcrumb,
		// 	'title'      => $this->title,
		// 	'link'				 => $this->url,
		// ];
    // array_merge($data, $additional);

    if(count($results['data'] > 0 )){
      $results['state_code'] = 200;
      $results['success'] = true;
    }

		return response()->json($results, $results['state_code']);
	}

  public function inputValidate($inputs, $rules)
  {
    $validator = Validator::make($inputs, $rules);
		// Validation fails?
      $respon = $this->responses;
		if ($validator->fails()){
			$respon['state_code'] = 400;
      $respon['messages'] = Array($validator->messages()->first());
      return response()->json($respon, $respon['state_code']);
    }
  }

  public static function getFilter($request)
  {
    $filter = new \stdClass();
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    $filter->ip = $ip;

    // Custom filter.
    $filter->filter = (object)$request->input('filter');

    // Filter Date
    $tempDate = new \StdClass;
    if($request->input('filterDate')){
      $filterDate = explode(" to ",$request->input('filterDate'));
      $tempDate->from = $filterDate[0];
      $tempDate->to = $filterDate[1] ?? $filterDate[0];

      $filter->filterDate = $tempDate;
    }
    
    // Columns.
    $columns = $request->input('columns') == null ? array() : $request->input('columns');
    
    // Filter columns.
    $filter->filterColumn = $request->input('filterColumn') ?? null;
    $filter->filterText = $request->input('filterText') ?? null;
    
    // Sort columns.
    $filter->sortColumns = array();
    $orderColumns = $request->input('order') != null ? $request->input('order') : array();
    foreach ($orderColumns as $value){
      $sortColumn = new \stdClass();
      $sortColumn->field = $columns[$value['column']]['data'];
      if (empty($sortColumn->field)) continue;
      
      $sortColumn->dir = $value['dir'];
      array_push($filter->sortColumns, $sortColumn);
    }
    
    // Paging.
    $filter->pageLimit = $request->input('length') ?: 1;
    $filter->pageOffset = $request->input('start') ?: 0;
    
    // Log::info(json_encode($filter));
    return $filter;
  }
}
