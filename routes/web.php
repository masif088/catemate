<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your applicat   ion. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\User;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', function (){
//    $sex=1;
//    $race_id=1;
//    $user = User::find(1);
////        return $request;
//
//    $query = "SELECT  TIMESTAMPDIFF(month, cats.birth, CURDATE()) as age ,TIMESTAMPDIFF(day, cats.last_parasite, CURDATE()) as parasite, cats.vaccine, users.id as user_id,cats.id,cats.name,cats.birth,cats.photo,races.title as race,
//                    ( 6371 * acos( cos( radians($user->latitude) )
//                    * cos( radians( users.latitude ) )
//                    * cos( radians( users.longitude ) - radians($user->longitude) )
//                    + sin( radians($user->latitude) ) * sin(radians(users.latitude)))) AS distance
//                    FROM cats
//                    LEFT JOIN users ON users.id = cats.user_id
//                    LEFT JOIN races ON races.id = cats.race_id
//                    where cats.status=1 and users.status=1 and cats.user_id!=$user->id
//                    ";
//
//    if ($sex == 1) {
//        //laki
//        $query = $query . "and cats.sex = 2 and TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 12";
//        $query = $query . " and TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
//    } else {
//        //perempuan
//        $query = $query . "and cats.sex = 1 and TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 6";
//        $query = $query . " and TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
//    }
////        if ($request->vaccine != null) {
////            $query = $query . " and vaccine = " . $request->vaccine;
////        }
////        if ($request->parasite != null) {
//    $query = $query . " and TIMESTAMPDIFF(day, cats.last_parasite, CURDATE()) >= 7";
//    $query = $query . " and TIMESTAMPDIFF(day, cats.last_parasite, CURDATE()) <= 90";
////        }//7-90 hari
////        if ($request->race != null) {
////            $query = $query . "and race_id = " . $request->race;
////        }
//    $query = $query . " having distance <= " . 25;
////        if ($request->race != null) {
////        $query = $query . " ORDER BY FIELD(race_id, $request->race) DESC";
////        }
////        $query = $query . " ,cats.last_parasite, cats.birth,vaccine  DESC";
////        return $query;
////        order by parasite -> umur -> vaccine ->
//    $res=DB::select(DB::raw($query));
//    foreach ($res as$r){
//        if ($r->parasite>=7 &&$r->parasite<=23){
//            $par=5;
//        }elseif ($r->parasite>=24 &&$r->parasite<=40){
//            $par=4;
//        }elseif ($r->parasite>=41 &&$r->parasite<=57){
//            $par=3;
//        }elseif ($r->parasite>=58 &&$r->parasite<=73){
//            $par=2;
//        }else{
//            $par=1;
//        }
//
//        if ($r->age <= 12 && $r->age <= 36){
//            $age=5;
//        }elseif ($r->age <= 36 && $r->age <= 60){
//            $age=4;
//        }elseif ($r->age <= 10 && $r->age < 12){
//            $age=3;
//        }elseif ($r->age > 60 ){
//            $age=2;
//        }else{
//            $age=1;
//        }
//        if ($r->race==$race_id){
//            $race=1;
//        }else{
//            $race=0;
//        }
//        $r->pf=(($par+$age+$r->vaccine)/3)*0.7+($race/1)*0.3;
//    }
//    usort($res,function($a,$b){
//        return $a->pf <=> $b->pf;
//    });
//    return $res;
//})->name('home');
