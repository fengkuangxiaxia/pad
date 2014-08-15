<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

/*
|--------------------------------------------------------------------------
| 基础权限
|--------------------------------------------------------------------------
*/
Route::group(array('prefix' => 'auth'), function () {
    $Authority = 'AuthorityController@';
    # 退出
    Route::get('logout', array('as' => 'logout', 'uses' => $Authority.'getLogout'));
    Route::group(array('before' => 'guest'), function () use ($Authority) {
        # 登录
        Route::get(                   'signin', array('as' => 'signin'        , 'uses' => $Authority.'getSignin'));
        Route::post(                  'signin', $Authority.'postSignin');
        # 注册
        Route::get(                   'signup', array('as' => 'signup'        , 'uses' => $Authority.'getSignup'));
        Route::post(                  'signup', $Authority.'postSignup');
    });
    Route::group(array('before' => 'auth'), function () use ($Authority) {
        # 密码修改
        Route::get(  'changePassword', array('as' => 'changePassword', 'uses' => $Authority.'getChangePassword'));
        Route::post( 'changePassword', $Authority.'postChangePassword');
    });
});

/*
|--------------------------------------------------------------------------
| 首页
|--------------------------------------------------------------------------
*/
Route::group(array(), function () {
    $Home = 'HomeController@';
    # 首页
    Route::get(            '/', array('as' => 'home'            , 'uses' => $Home.'showWelcome'));
});

/*
|--------------------------------------------------------------------------
| 宠物
|--------------------------------------------------------------------------
*/
Route::group(array('prefix' => 'monster', 'before' => 'auth'), function () {
    $Monster = 'MonsterController@';
    # 宠物首页
    Route::get('index', array('as' => 'monster.index', 'uses' => $Monster.'getIndex'));
    Route::post('index', $Monster.'saveMonsters');
    # 获取用户宠物
    Route::get('userMonster', array('as' => 'userMonster', 'uses' => $Monster.'getUserMonster'));
});

/*
|--------------------------------------------------------------------------
| 队伍
|--------------------------------------------------------------------------
*/
Route::group(array('prefix' => 'team', 'before' => 'auth'), function () {
    $Team = 'TeamController@';
    # 队伍首页
    Route::get('index', array('as' => 'team.index', 'uses' => $Team.'getIndex'));
    # 获取地下城列表
    Route::get('dungeon', array('as' => 'dungeon', 'uses' => $Team.'getDungeons'));
    # 获取地下城匹配的队伍
    Route::post('index', $Team.'getTeams');
});
