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
    'middleware' => ['serializer:array','bindings']
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
        $api->get('categories', 'CategoriesController@index')
            ->name('api.categories.index');
        $api->get('topics', 'TopicsController@index')
            ->name('api.topics.index');
        $api->get('users/{user}/topics', 'TopicsController@userIndex')
            ->name('api.users.topics.index');
        $api->get('topics/{topic}', 'TopicsController@show')
        ->name('api.topics.show');

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
            // 发布话题
            $api->post('topics', 'TopicsController@store')
                ->name('api.topics.store');
            // 修改话题
            $api->patch('topics/{topic}', 'TopicsController@update')
                ->name('api.topics.update');
            // 删除话题
            $api->delete('topics/{topic}', 'TopicsController@destroy')
                ->name('api.topics.destroy');
             // 发布回复
            $api->post('topics/replies/{topic}', 'RepliesController@store')
                ->name('api.topics.replies.store');
            // 删除回复
            // $api->delete('topics/replies/{reply}/{topic}', 'RepliesController@destroy')
            //     ->name('api.topics.replies.destroy');
            $api->delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')
                ->name('api.topics.replies.destroy');
            $api->get('topics/{topic}/replies', 'RepliesController@index')
                ->name('api.topics.replies.index');
            $api->get('users/{user}/replies', 'RepliesController@userIndex')
                ->name('api.users.replies.index');
            // 通知列表
            $api->get('user/notifications', 'NotificationsController@index')
                ->name('api.user.notifications.index');
            // 通知统计
            $api->get('user/notifications/stats', 'NotificationsController@stats')
                ->name('api.user.notifications.stats');
            // 标记消息通知为已读
            $api->patch('user/read/notifications', 'NotificationsController@read')
                ->name('api.user.notifications.read');
        });
	});
});