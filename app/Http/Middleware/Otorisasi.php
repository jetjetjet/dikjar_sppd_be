<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class Otorisasi
{
    protected $responses = array('state_code' => 401, 'success' => false, 'messages' => array(), 'data' => Array());
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$actions)
    {
        // $user = Auth::user();

        if($request->user()->tokenCan('is_admin')){
          return $next($request);
        }

        foreach($actions as $act) {
            if ($request->user()->tokenCan($act)) 
            {
              return $next($request);
            }
        }
        
        $respons = $this->responses;
        $respons['message'] = ['Perintah tidak dapat dijalankan!'];
        return response()->json($respons, 401);
    }
}
