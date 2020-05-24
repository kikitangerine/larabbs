<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;

    // protected $user;
    // public function __construct(){
    // 	parent::__construct();
    // 	$this->user = \Auth::guard('api')->user();
    // }

    protected function user()
    {
    	return \Auth::guard('api')->user();
    }
}
