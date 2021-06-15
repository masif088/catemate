<?php

use App\Cat;
use App\CatPhoto;
use App\Mating;
use App\Race;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// use Illuminate\Routing\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login','Api\AuthController@login');
Route::post('register','Api\AuthController@register');
Route::post('check/login','Api\AuthController@checkLogin');
Route::post('logout','Api\AuthController@logout');
Route::post('update/location','Api\AuthController@updateLocation');
Route::post('update/profile','Api\AuthController@updateProfile');
Route::post('update/profile/photo','Api\AuthController@updateProfilePhoto');

Route::get('cat/race','Api\CatController@catRace');

Route::post('cat/store','Api\CatController@catStore');
Route::post('cat/update','Api\CatController@catUpdate');
Route::post('cat/update/status','Api\CatController@catUpdateStatus');
Route::post('cat/update/photo','Api\CatController@catUpdatePhoto');
Route::post('cat/remove/photo','Api\CatController@catRemovePhoto');
Route::post('cat/add/photo','Api\CatController@catAddPhoto');
Route::post('cat/add/photo','Api\CatController@chatList');

Route::get('cat/me/love/{cat}','Api\MateController@catLove');
Route::get('cat/me/married/{cat}','Api\MateController@catMarried');
Route::post('cat/me/search','Api\MateController@catSearch');
Route::post('cat/me/mating','Api\MateController@catMeMating');

Route::get('cat/me/{user}/{cat}','Api\CatController@catMeDetail');
Route::get('cat/me/{user}','Api\CatController@catMe');

Route::get('chat/{user}','Api\MateController@chatList');
Route::post('chat','Api\MateController@chat');
Route::post('chat/last-chat','Api\MateController@lastChat');
Route::post('chat/status-mate','Api\MateController@statusMate');
Route::post('chat/status-chat','Api\MateController@statusChat');
Route::post('chat/user-read','Api\MateController@readChat');
