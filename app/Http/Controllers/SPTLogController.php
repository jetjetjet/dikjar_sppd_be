<?php

namespace App\Http\Controllers;

use DB;
use App\Models\SPTLog;
use Illuminate\Http\Request;

class SPTLogController extends Controller
{
	public function grid(Request $request)
	{
		$filter = $this->getFilter($request);
		$results = $this->responses;
		$q= SPTLog::join('spt', 'spt.id', 'reference_id')
		->select(
			'spt.no_spt',
			'username',
			'reference_id',
			'aksi',
			'success',
			'detail',
			DB::raw("to_char(spt_log.created_at, 'DD-MM-YYYY HH:MI') as tanggal")
		);

		//total count
		$count = $q->count();

		//filter column
		if(count($filter->filter) > 0){
      foreach($filter->filter as $val) {
				$trimmedText = trim($val->value);
				$filterCol = $val->column;
	
				$text = strtolower(implode('%', explode(' ', $trimmedText)));
				$q = $q->whereRaw('lower('.$filterCol .') like (?)', [ '%' . $text . '%']);
			}
    }

    $countFiltered = $q->count();

		// order
		if(count($filter->sortColumns) > 0){
			$sorting = $filter->sortColumns;
			$trimmedText = trim($sorting[0]->field);
			$orderBy = $sorting[0]->type == 'none' ? 'desc' : $sorting[0]->type;

			$text = strtolower(implode('%', explode(' ', $trimmedText)));
			$q = $q->orderBy($text, $orderBy);
		} else {
			$q = $q->orderBy('spt_log.created_at', 'DESC');
		}
		
		// $q = $q->skip($filter->pageOffset)
		// ->take($filter->pageLimit)
		// ->get();

		$results['data'] = $q->paginate($filter->pageLimit);

		$results['state_code'] = 200;
		$results['success'] = true;
		$results['messages'] = $filter;

		return response()->json($results, $results['state_code']);
	}
}