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

//detail kucing
Route::get('/cat/me/{user_id}/{id}', function ($user_id, $id) {
    return Cat::with('race')->find($id);
});

Route::get('/cat/me/{user_id}', function ($user_id) {
    return response(Cat::whereUserId($user_id)->with('race')->get());
});

Route::get('/cat/race', function () {
    return Race::get(['id', 'title']);
});

Route::post('register', function (Request $request) {
    $validate = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
        'address' => 'required',
    ]);
    if ($validate->fails()) {
        $respon = [
            'status' => 'error',
            'msg' => 'Validator error',
            'errors' => $validate->errors(),
            'content' => null,
        ];
        return response()->json($respon, 200);
    }
    $tokenResult=Str::random(60);
    $user=User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'address' => $request->address,
        'remember_token'=>$tokenResult
    ]);
    $respon = [
        'status' => 'success',
        'msg' => 'Berhasil mendaftar',
        'errors' => null,
        'content' => [
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' => $user
        ]
    ];
    return response()->json($respon, 200);

});

Route::post('login', function (Request $request) {
    $validate = Validator::make($request->all(), [
        'email' => 'required',
        'password' => 'required',
    ]);

    if ($validate->fails()) {
        $respon = [
            'status' => 'error',
            'msg' => 'harap isi email dan password',
            'errors' => $validate->errors(),
            'content' => null,
        ];
        return response()->json($respon, 400);
    }

    $user = User::where('email', $request->email)->first();
    if ($user == null) {
        $respon = [
            'status' => 'yang bener woi',
            'msg' => 'email tidak ditemukan',
            'errors' => 'email is not register',
            'content' => null
        ];
        return response()->json($respon, 200);
    }
    if (!Hash::check($request->password, $user->password, [])) {
        $respon = [
            'status' => 'failed',
            'msg' => 'password anda salah',
            'errors' => 'wrong password',
            'content' => null
        ];
        return response()->json($respon, 200);
    }
    $user->setRememberToken($token = Str::random(60));
//    $this->provider->updateRememberToken($user, $token);
    $tokenResult = $user->remember_token;
    $respon = [
        'status' => 'success',
        'msg' => 'Berhasil masuk',
        'errors' => null,
        'content' => [
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' => $user
        ]
    ];
    return response()->json($respon, 200);
});

//kucing disukai
Route::get('/cat/me/{id}/loved', function ($id) {
    return Mating::with('cat_2')->where('status', '=', '1')
        ->where('cat_id_1', '=', $id)
        ->get();
});

//kucing dinikai
Route::get('/cat/me/{id}/maried', function ($id) {
    return Mating::with('cat_1', 'cat_2')->where('status', '=', '1')
        ->where(function ($q) use ($id) {
            return $q->where('cat_id_1', '=', $id)->orWhere('cat_id_2', '=', $id);
        })
        ->get();
});
//buat kucing
Route::post('cat', function (Request $request) {
//    $request->toArray();
//    $respon = [
//        'status' => 'success',
//        'msg' => 'Berhasil menambahkan kucing',
//        'errors' => null,
//    ];
//    return response()->json($respon, 200);
    $file = $request->file('file');
    $filename = Str::slug( $request->name  . '-' . date('Hms') ) . '.' . $request->file('file')->getClientOriginalExtension();
    Storage::disk('local')->put('public/cat_photo/' . $filename, file_get_contents($file));
    try {
    $cat = Cat::create([
        'name' => $request->name,
        'user_id' => $request->user_id,
        'race_id' => $request->race_id,
        'birth' => $request->birth,
        'sex' => $request->sex,
        'photo' => 'cat_photo/' . $filename
    ]);
    } catch (\Illuminate\Database\QueryException $exception) {
        // You can check get the details of the error using `errorInfo`:
        $errorInfo = $exception->errorInfo;
        \App\Log::creat(["log"=>$errorInfo]);
        $respon = [
            'status' => 'success',
            'msg' => $errorInfo,
            'errors' => null,
        ];

        return response()->json($respon, 200);
    }
    $respon = [
        'status' => 'success',
        'msg' => 'Berhasil menambahkan kucing',
        'errors' => null,
    ];
    return response()->json($respon, 200);
});
Route::get('/asdasd',function (){
    $respon = [
        'status' => 'success',
        'msg' => 'Berhasil menambahkan kucing',
        'errors' => null,
    ];
    return response()->json($respon, 200);
});
Route::post('/cat/edit', function (Request $request) {
    Cat::find($request->cat_id)->update([
        'name' => $request->name,
        'race_id' => $request->race_id,
        'birth' => $request->birth,
        'vaccine' => $request->vaccine,
        'last_parasite' => $request->last_parasite,
        'last_vaccine' => $request->last_vaccine,
        'sex' => $request->sex,
    ]);
    $respon = [
        'status' => 'success',
        'msg' => 'Berhasil mengubah kucing',
        'errors' => null,
    ];
    return response()->json($respon, 200);
});

Route::post('/cat/edit/status',function (Request $request){
    Cat::find($request->cat_id)->update([
       'status'=>$request->status
    ]);
    $respon = [
        'status' => 'success',
        'msg' => 'Berhasil mengubah status kucing',
        'errors' => null,
    ];
    return response()->json($respon, 200);
});

Route::post('/cat/edit/main-photo',function (Request $request){
    $cat=Cat::find($request->cat_id);
    Storage::disk('local')->delete('public/' . $cat->photo);
    $file = $request->file('photo');
    $filename = Str::slug($request->user_id . '-' . $request->name . date . '-' . ('Hms') . rand(100)) . '.' . $request->file('file')->getClientOriginalExtension();
    Storage::disk('local')->put('public/cat_photo/' . $filename, $file, 'public');
    $cat->update([
        'photo' => 'cat_photo/' . $filename
    ]);
    $respon = [
        'status' => 'success',
        'msg' => 'Berhasil mengubah foto kucing',
        'errors' => null,
    ];
    return response()->json($respon, 200);
});

Route::post('/cat/remove/sekunder-photo',function (Request $request){
    Storage::disk('local')->delete('public/' . $request->path_photo);
});

Route::post('/cat/add/sekunder-photo',function (Request $request){
    $file = $request->file('photo');
    $filename = Str::slug( $request->cat_id  . '-' . date('Hms') . rand(100)) . '.' . $request->file('file')->getClientOriginalExtension();
    Storage::disk('local')->put('public/cat_photo/' . $filename, $file, 'public');
    CatPhoto::create([
        'cat_id'=>$request->cat_id,
        'path'=>$filename
    ]);
});


Route::post('/cat/search', function (Request $request) {
    $query = "SELECT
    ( 6371 * acos( cos( radians(37) )
                  * cos( radians( users.latitude ) )
                  * cos( radians( users.longitude ) - radians(-122) )
                  + sin( radians(37) ) * sin(radians(users.latitude)) ) ) AS distance
    FROM cats
    LEFT JOIN users ON users.id = cats.user_id
    where cats.status=1";
    //        if ($request->age_start != null){
//        if ($request->age_end != null){
    if ($request->sex == 1) {
        //laki
        $query = $query . "and TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 12";
        $query = $query . "and TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
    } else {
        //perempuan
        $query = $query . "and TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 6";
        $query = $query . "and TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
    }

//    if($request->weights_start != null){
//        $query= $query . "and weight >= ".$request->weights_start;
//    }
//    if($request->weights_end != null){
//        $query = $query . "and weight <= ".$request->weights_end;
//    }
    if ($request->vaccine != null) {
        $query = $query . "and vaccine = " . $request->vaccine;
    }
    if ($request->parasite != null) {
        $query = $query . "and vaccine = " . $request->vaccine;
    }//7-90 hari
    if ($request->race != null) {
        $query = $query . "and race_id = " . $request->race;
    }
    $query = $query . " HAVING distance <= " . $request->distance;
    //order by parasite -> umur -> vaccine -> ras
    return response(DB::select(DB::raw($query)));
});

// Route::get('/');
