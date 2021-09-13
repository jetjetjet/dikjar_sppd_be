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
}
