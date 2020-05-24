<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
	'namespace'=>'App\Http\Controllers\Api',
    'middleware' => 'serializer:array'
], function($api){
	$api->group([
		'middleware'=>'api.throttle',
		'limit'=>config('api.throttling.rate_limits.sign.limit'),
		'expires'=>config('api.throttling.rate_limits.sign.expires'),
	], function($api){
		$api->post('verificationCodes', 'VerificationCodesController@store')
			->name('api.verificationCodes.store');
		$api->post('users', 'UsersController@store')
			->name('api.users.store');
		// 图片验证码
    	$api->post('captchas', 'CaptchasController@store')
        	->name('api.captchas.store');
        //第三方登录
        $api->post('authorizations/url', 'AuthorizationsController@getAuthUrl')
            ->name('authorizations.get_auth_url');

        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
        	->where('social_type', 'weixin')
        	->name('socials.authorizations.store');
        // 登录
    	$api->post('authorizations', 'AuthorizationsController@store')
        	->name('api.authorizations.store');
        // 刷新token
    	$api->put('authorizations/current', 'AuthorizationsController@update')
        	->name('authorizations.update');
        // 删除token
    	$api->delete('authorizations/current', 'AuthorizationsController@destroy')
        	->name('authorizations.destroy');

        //用户数据
        // 某个用户的详情
        $api->get('users/{user}', 'UsersController@show')
            ->name('users.show');

        // 登录后可以访问的接口
        $api->group([
            'middleware'=>'auth:api',
        ],function($api){
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            // 图片资源
            $api->post('images', 'ImagesController@store')
                ->name('api.images.store');
            // 编辑登录用户信息
            $api->patch('user', 'UsersController@update')
                ->name('api.user.update');
        });

	});
});