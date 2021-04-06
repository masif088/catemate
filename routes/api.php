<?php

use App\Cat;
use App\CatPhoto;
use App\Mating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

//detail kucing
Route::get('/cat/me/{id}',function($id){
    return Cat::with(['user'])->find($id);
});

//kucing disukai
Route::get('/cat/me/{id}/loved', function ($id) {
    return Mating::with('cat_2')->where('status','=','1')
    ->where('cat_id_1','=',$id)
    ->get();
});

//kucing dinikai
Route::get('/cat/me/{id}/maried', function ($id) {
    return Mating::with('cat_1','cat_2')->where('status','=','1')
    ->where(function ($q) use ($id) {
        return $q->where('cat_id_1','=',$id)->orWhere('cat_id_2','=',$id);
    })
    ->get();
});
//buat kucing
Route::post('cat', function (Request $request) {
    $cat=Cat::create([
        'name'=>$request->name,
        'user_id'=>$request->user_id,
        'race_id'=>$request->race_id,
        'status'=>$request->status,
        'weight'=>$request->weight,
        'birth'=>$request->birth,
        'vaccine'=>$request->vaccine,
        'cat_photo_1'=>$request->cat_photo_1,
        'cat_photo_2'=>$request->cat_photo_2,
        'cat_photo_3'=>$request->cat_photo_3,
        'cat_photo_4'=>$request->cat_photo_4,
        'cat_photo_5'=>$request->cat_photo_5,
    ]);
});
Route::post('/cat/edit',function(Request $request){
    Cat::find($request->user_id)->update([
        'name'=>$request->name,
        'race_id'=>$request->race_id,
        'status'=>$request->status,
        'weight'=>$request->weight,
        'birth'=>$request->birth,
        'vaccine'=>$request->vaccine,
        'cat_photo_1'=>$request->cat_photo_1,
        'cat_photo_2'=>$request->cat_photo_2,
        'cat_photo_3'=>$request->cat_photo_3,
        'cat_photo_4'=>$request->cat_photo_4,
        'cat_photo_5'=>$request->cat_photo_5,
    ]);
    return true;
});


Route::post('/cat/search',function(Request $request){
    $query="SELECT
    ( 6371 * acos( cos( radians(37) )
                  * cos( radians( users.latitude ) )
                  * cos( radians( users.longitude ) - radians(-122) )
                  + sin( radians(37) ) * sin(radians(users.latitude)) ) ) AS distance
    FROM cats
    LEFT JOIN users ON users.id = cats.user_id
    where cats.status=1";
    if ($request->age_start != null){
        $query= $query . "and TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= ". $request->age_start;
    }
    if ($request->age_end != null){
        $query= $query . "and TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= ". $request->age_end;
    }
    if($request->weights_start != null){
        $query= $query . "and weight >= ".$request->weights_start;
    }
    if($request->weights_end != null){
        $query = $query . "and weight <= ".$request->weights_end;
    }
    if($request->vaccine != null){
        $query= $query . "and vaccine = ".$request->vaccine;
    }
    if($request->race != null){
        $query= $query . "and race_id = ".$request->race;
    }
    $query= $query . " HAVING distance <= ". $request->distance;
    return response(DB::select(DB::raw($query)));
});

// Route::get('/');
