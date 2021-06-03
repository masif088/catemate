<?php

namespace App\Http\Controllers\Api;

use App\Mating;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MateController extends Controller
{
    public function catLove($id){
        return Mating::with('cat_2')->where('status', '=', '1')
            ->where('cat_id_1', '=', $id)
            ->get();
    }
    public function catMarried($id){
        return Mating::with('cat_1', 'cat_2')->where('status', '=', '2')
            ->where(function ($q) use ($id) {
                return $q->where('cat_id_1', '=', $id)->orWhere('cat_id_2', '=', $id);
            })
            ->get();
    }
    public function catSearch(Request $request){
        $user=User::find($request->user_id);
        $query = "SELECT cats.id,cats.name,cats.birth,cats.photo,races.title as race,
                    ( 6371 * acos( cos( radians($user->latitude) )
                    * cos( radians( users.latitude ) )
                    * cos( radians( users.longitude ) - radians($user->longitude) )
                    + sin( radians($user->latitude) ) * sin(radians(users.latitude)))) AS distance
                    FROM cats
                    LEFT JOIN users ON users.id = cats.user_id
                    LEFT JOIN races ON races.id = cats.race_id
                    where cats.status=1 and users.status=1 and cats.user_id!=$user->id
                    ";

        if ($request->sex == 1) {
            //laki
            $query = $query . "and cats.sex = 2 and TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 12";
            $query = $query . " and TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
        } else {
            //perempuan
            $query = $query . "and cats.sex = 1 and TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 6";
            $query = $query . " and TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
        }
//        if ($request->vaccine != null) {
//            $query = $query . " and vaccine = " . $request->vaccine;
//        }
        if ($request->parasite != null) {
            $query = $query . " and TIMESTAMPDIFF(month, cats.last_parasite, CURDATE()) >= 7";
            $query = $query . " and TIMESTAMPDIFF(month, cats.last_parasite, CURDATE()) <= 90";
        }//7-90 hari
//        if ($request->race != null) {
//            $query = $query . "and race_id = " . $request->race;
//        }
        $query = $query . " having distance <= " . $request->distance;
        if ($request->race != null) {
            $query=$query." ORDER BY FIELD(race_id, $request->race) DESC";
        }
        $query=$query." ,cats.last_parasite, cats.birth,vaccine  DESC";
        return $query;
        //order by parasite -> umur -> vaccine ->
//        return response(DB::select(DB::raw($query)));
    }
}
