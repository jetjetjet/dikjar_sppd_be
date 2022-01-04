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
    // Custom filter.
    $colFilters = json_decode($request->input('columnFilters'));

    $filter->filter = array();
    if($colFilters != null) {
      foreach($colFilters as $key => $val) {
        $tempFilter = new \stdClass();
  
        $tempFilter->column = $key;
        $tempFilter->value = $val;
        array_push($filter->filter, $tempFilter);
      }
    }

    // Filter Date
    $tempDate = new \StdClass;
    if($request->input('filterDate')){
      $filterDate = explode(" to ",$request->input('filterDate'));
      $tempDate->from = $filterDate[0];
      $tempDate->to = $filterDate[1] ?? $filterDate[0];

      $filter->filterDate = $tempDate;
    }
    
    // Sort columns.
    $filter->sortColumns = array();
    $orderColumns = $request->input('sort') != null ? $request->input('sort') : array();
    foreach ($orderColumns as $order){
      $value = json_decode($order);;
      array_push($filter->sortColumns, $value);
    }
    
    // Paging.
    $filter->pageLimit = (int)$request->input('perPage') ?: 1;
    $filter->pageOffset = (int)$request->input('page') ?: 0;
    
    // Log::info(json_encode($filter));
    return $filter;
  }
}
