<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Log;
use App\Mating;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MateController extends Controller
{
    public function catLove($id)
    {
        return Mating::with('cat_2.race')->where('status_mate', '=', '3')
            ->where('cat_id_1', '=', $id)
            ->get();
    }

    public function catMarried($id)
    {
        return Mating::with('cat_1.race', 'cat_2.race')->where('status_mate', '=', '1')
            ->where(function ($q) use ($id) {
                return $q->whereHas('cat_1', function ($q2) use ($id) {
                    $q2->whereUserId($id);
                })->orWhereHas('cat_2', function ($q2) use ($id) {
                    $q2->whereUserId($id);
                });
            })
            ->get();
    }

    public function chatList($user_id)
    {
        return Mating::with('cat_1', 'cat_2', 'cat_1.user', 'cat_2.user')
            ->whereIn('status_chat', [1])
            ->where(function ($q) use ($user_id) {
                $q->whereHas('cat_1', function ($q) use ($user_id) {
                    return $q->whereUserId($user_id);
                })->orWhereHas('cat_2', function ($q) use ($user_id) {
                    return $q->whereUserId($user_id);
                });
            })->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getMating($mating)
    {

    }

    public function catMeMating(Request $request)
    {
        $mating = Mating::where("cat_id_1", $request->cat_id_1)->where("cat_id_2", $request->cat_id_2)->with('cat_1', 'cat_2', 'cat_1.user', 'cat_2.user')->first();
        if ($mating != null) {
            $mating->update([
                'status_mate' => $request->status_mate,
                'status_chat' => $request->status_chat
            ]);
        } else {
            $mating = Mating::create([
                'cat_id_1' => $request->cat_id_1,
                'cat_id_2' => $request->cat_id_2,
                'status_mate' => $request->status_mate,
                'status_chat' => $request->status_chat
            ]);
//            $mating=Mating::find($mating->id)->with('cat_1','cat_2', 'cat_1.user', 'cat_2.user');
//            return var_dump($mating);
        }

//        if()
        $msg = "";
        if ($request->status_mate == 3) {
            $msg = "berhasil ditambahkan ke favorit";
        } elseif ($request->status_chat == 1) {
            $msg = "cek";
        } elseif ($request->status = 2) {
            $msg = "berhasil melakukan mating";
        } elseif ($request->status = 3) {
            $msg = "mating dibatalkan";
        }
        return [
            'status' => 'success',
            'msg' => $msg,
            'errors' => null,
            'mating' => $mating
        ];
    }

    public function catMeMatingChanging(Request $request)
    {
        $mating = Mating::where("cat_id_1", $request->cat_id_1)->where("cat_id_2", $request->cat_id_2)->update([
            'status' => $request->status
        ]);
        $msg = "";
        if ($request->status = 0) {
            $msg = "berhasil menghapus";
        } elseif ($request->status = 4) {
            $msg = "berhasil mengubah ke favorit";
        } elseif ($request->status = 3) {
            $msg = "melakukan chat";
        } elseif ($request->status = 2) {
            $msg = "berhasil melakukan mating";
        } elseif ($request->status = 3) {
            $msg = "mating dibatalkan";
        }
        return [
            'status' => 'success',
            'msg' => $msg,
            'errors' => null,
        ];
    }

    public function catSearch(Request $request)
    {
        Log::create(['log'=>$request->distance]);
        $user = User::find($request->user_id);
        Log::create(['log'=>$request->distance]);
        //haversine
        $query = "SELECT  TIMESTAMPDIFF(month, cats.birth, CURDATE()) as age ,
               TIMESTAMPDIFF(day, cats.last_parasite, CURDATE()) as parasite,
               cats.vaccine, users.id as user_id,cats.id,cats.name,
               cats.birth,cats.photo,races.title as race,
                    ( 6371 * acos( cos( radians($user->latitude) )
                    * cos( radians( users.latitude ) )
                    * cos( radians( users.longitude ) - radians($user->longitude) )
                    + sin( radians($user->latitude) ) * sin(radians(users.latitude)))) AS distance
                FROM cats
                LEFT JOIN users ON users.id = cats.user_id
                LEFT JOIN races ON races.id = cats.race_id
                WHERE cats.status=1 AND users.status=1 AND cats.user_id!=$user->id";
        if ($request->sex == 1) {
            $query = $query . " AND cats.sex = 2 AND TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 12";
            $query = $query . " AND TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
        } else {
            $query = $query . " AND cats.sex = 1 AND TIMESTAMPDIFF(month, cats.birth, CURDATE()) >= 6";
            $query = $query . " AND TIMESTAMPDIFF(month, cats.birth, CURDATE()) <= 96";
        }
        $query = $query . " AND TIMESTAMPDIFF(day, cats.last_parasite, CURDATE()) >= 7";
        $query = $query . " AND TIMESTAMPDIFF(day, cats.last_parasite, CURDATE()) <= 90";
        $query = $query . " having distance <= " . $request->distance;

        Log::create(['log'=>$query]);
        $res = DB::select(DB::raw($query));

        Log::create(['log'=>$query]);

        Log::create(['log'=>json_encode($res)]);
        //profile matching
        foreach ($res as $r) {
            if ($r->parasite >= 7 && $r->parasite <= 23) {
                $par = 5;
            } elseif ($r->parasite >= 24 && $r->parasite <= 40) {
                $par = 4;
            } elseif ($r->parasite >= 41 && $r->parasite <= 57) {
                $par = 3;
            } elseif ($r->parasite >= 58 && $r->parasite <= 73) {
                $par = 2;
            } else {
                $par = 1;
            }

            if ($r->age <= 12 && $r->age <= 36) {
                $age = 5;
            } elseif ($r->age <= 36 && $r->age <= 60) {
                $age = 4;
            } elseif ($r->age <= 10 && $r->age < 12) {
                $age = 3;
            } elseif ($r->age > 60) {
                $age = 2;
            } else {
                $age = 1;
            }
            if ($r->race == $request->race) {
                $race = 1;
            } else {
                $race = 0;
            }
            $r->pf = (($par + $age + $r->vaccine) / 3) * 0.7 + ($race / 1) * 0.3;
        }
        usort($res, function ($a, $b) {
            return $a->pf <=> $b->pf;
        });

        return array_reverse($res);
    }

    public function lastChat(Request $request)
    {
        if ($request->sender == 1) {
            Mating::find($request->id)
                ->update([
                    "last_chat" => $request->last_chat,
                    "user_id_1_read" => 1,
                    "user_id_2_read" => 0,
                ]);
        } else {
            Mating::find($request->id)
                ->update([
                    "last_chat" => $request->last_chat,
                    "user_id_1_read" => 0,
                    "user_id_2_read" => 1,
                ]);
        }
        return [
            'status' => 'success',
            'msg' => "",
            'errors' => null,
        ];
    }

    public function statusMate(Request $request)
    {
        Mating::find($request->id)->update([
            "status_mate" => $request->status_mate
        ]);
        return [
            'status' => 'success',
            'msg' => "berhasil mengubah status mate",
            'errors' => null,
        ];
    }

    public function statusChat(Request $request)
    {
        Mating::find($request->id)->update([
            "status_chat" => $request->status_chat
        ]);
        return [
            'status' => 'success',
            'msg' => "Berhasil melakukan aksi",
            'errors' => null,
        ];
    }

    public function readChat(Request $request)
    {
        if ($request->reader == 1) {
            Mating::find($request->id)
                ->update([
                    "user_id_1_read" => 1,
                ]);
        } else {
            Mating::find($request->id)
                ->update([
                    "user_id_2_read" => 1,
                ]);
        }
        return [
            'status' => 'success',
            'msg' => "",
            'errors' => null,
        ];
    }


}
