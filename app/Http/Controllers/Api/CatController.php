<?php

namespace App\Http\Controllers\Api;

use App\Cat;
use App\CatPhoto;
use App\Log;
use App\Race;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatController extends Controller
{
    public function catMeDetail($user_id, $id)
    {
        return Cat::with('race','catPhotos','user')->find($id);
    }

    public function catMe($user_id)
    {
        return Cat::whereUserId($user_id)->with('race','user')->get();
    }

    public function catRace()
    {
        return Race::all(['id', 'title']);
    }

    public function catStore(Request $request)
    {
        $file = $request->file('file');
        $filename = Str::slug($request->name . '-' . date('Hms')) . '.' . $request->file('file')->getClientOriginalExtension();
        Storage::disk('local')->put('public/cat_photo/' . $filename, file_get_contents($file));
        Cat::create([
            'name' => str_replace('"', '', $request->name),
            'user_id' => $request->user_id,
            'race_id' => $request->race_id,
            'birth' => str_replace('"', '', $request->birth),
            'sex' => $request->sex,
            'photo' => 'cat_photo/' . $filename
        ]);
        $response = [
            'status' => 'success',
            'msg' => 'Berhasil menambahkan kucing',
            'errors' => null,
        ];
        return response()->json($response, 200);
    }

    public function catUpdate(Request $request)
    {
        Cat::find($request->cat_id)
            ->update([
                'name' => $request->name,
                'vaccine' => $request->vaccine,
                'last_parasite' => $request->last_parasite,
                'last_vaccine' => $request->last_vaccine,
            ]);
        $response = [
            'status' => 'success',
            'msg' => 'Berhasil mengubah kucing',
            'errors' => null,
        ];
        return response()->json($response, 200);
    }

    public function catUpdateStatus(Request $request)
    {
        Cat::find($request->cat_id)->update([
            'status' => $request->status
        ]);
        $respon = [
            'status' => 'success',
            'msg' => 'Berhasil mengubah status kucing',
            'errors' => null,
        ];
        return response()->json($respon, 200);
    }

    public function catUpdatePhoto(Request $request)
    {
        $cat = Cat::find(str_replace('"', '', $request->cat_id));
        Storage::disk('local')->delete('public/' . $cat->photo);
        $file = $request->file('file');
        $filename = Str::slug($cat->name . '-' . date('Hms')) . '.' . $request->file('file')->getClientOriginalExtension();
        Storage::disk('local')->put('public/cat_photo/' . $filename, file_get_contents($file));
        Log::create(["log"=>"cek 2"]);
        $cat->update([
            'photo' => 'cat_photo/' . $filename
        ]);
        Log::create(["log"=>"cek 3"]);
        $respon = [
            'status' => 'success',
            'msg' => 'Berhasil mengubah foto kucing',
            'errors' => null,
        ];
        return response()->json($respon, 200);
    }

    public function catRemovePhoto(Request $request)
    {
        CatPhoto::wherePath($request->path)->delete();
        Storage::disk('local')->delete('public/' . $request->path);
        $response = [
            'status' => 'success',
            'msg' => 'Berhasil menghapus foto kucing',
        ];
        return response()->json($response, 200);
    }

    public function catAddPhoto(Request $request)
    {
        Log::create(['log'=>'cek sinia']);
        $cat = Cat::find(str_replace('"', '', $request->cat_id));
        Storage::disk('local')->delete('public/' . $cat->photo);
        $file = $request->file('file');
        $filename = Str::slug($cat->name . '-' . date('Hms')) . '.' . $request->file('file')->getClientOriginalExtension();
        Storage::disk('local')->put('public/cat_photo/' . $filename, file_get_contents($file));
        Log::create(['log'=>'cek sini']);
        Log::create(['log'=>'cek b']);
        CatPhoto::create([
            'cat_id' => str_replace('"', '', $request->cat_id),
            'path' => 'cat_photo/' . $filename
        ]);
        Log::create(['log'=>'cek c']);
        $response = [
            'status' => 'success',
            'msg' => 'Berhasil menambahkan foto kucing',
        ];
        return $response;
    }
    public function catRemove(Request $request){
        Cat::find($request->cat_id)->delete();
        $response = [
            'status' => 'success',
            'msg' => 'Berhasil menghapus kucing',
        ];
        return response()->json($response, 200);
    }

}
