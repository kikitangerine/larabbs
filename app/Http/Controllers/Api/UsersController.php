<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Auth\AuthenticationException;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
    	$verifyData = \Cache::get($request->verification_key);

    	if(!$verifyData){
    		return $this->response->error('验证码已失效', 422);
    	}

    	//防止时序攻击
    	if(!hash_equals($verifyData['code'], $request->verification_code)){
            // throw new AuthenticationException('验证码错误');
    		return $this->response->errorUnauthorized('验证码错误');
    	}

    	$user = User::create([
    		'name' => $request->name,
    		'phone'=> $verifyData['phone'],
    		'password'=> bcrypt($request->password),
    	]);

    	//清理缓存
    	\Cache::forget($request->verification_key);

    	return $this->response->item($user, new UserTransformer())
        ->setMeta([
            'access_token' => \Auth::guard('api')->fromUser($user),
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ])
        ->setStatusCode(201);;
    }

    public function show(User $user, Request $request)
    {
        return new UserResource($user);
        // return $this->response->item($user, new UserTransformer());

    }

    public function me()
    {
        // $data = $this->user();
        $data = \Auth::guard('api')->user();
        // print_r($data);exit;
        return $this->response->item($data, new UserTransformer())->setStatusCode(201);
    }
}
