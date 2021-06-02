<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'address' => 'required',
        ]);
        if ($validate->fails()) {
            $respon = [
                'status' => 'error',
                'msg' => 'Mohon mengisi seluruh formnya',
                'errors' => $validate->errors(),
                'content' => null,
            ];
            return response()->json($respon, 200);
        }
        $tokenResult = Str::random(60);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => $request->address,
            'remember_token' => $tokenResult
        ]);
        $response = [
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
        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $response = [
                'status' => 'error',
                'msg' => 'harap isi email dan password',
                'errors' => $validate->errors(),
                'content' => null,
            ];
            return response()->json($response, 400);
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
            $response = [
                'status' => 'failed',
                'msg' => 'password anda salah',
                'errors' => 'wrong password',
                'content' => null
            ];
            return response()->json($response, 200);
        }
        $token = Str::random(60);
        $user->update(['remember_token'=>$token]);
//        $tokenResult = $user->remember_token;
        $respon = [
            'status' => 'success',
            'msg' => 'Berhasil masuk',
            'errors' => null,
            'content' => [
                'status_code' => 200,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ];
        return response()->json($respon, 200);
    }

    public function checkLogin(Request $request)
    {
        $user = User::whereRememberToken($request->token)->first();
        if ($user == null) {
            return [
                "msg" => "Token telah kadaluarsa",
                "errors" => "",
                "status" => "logout"
            ];
        } else {
            return [
                "msg" => "Selamat datang kembali",
                "errors" => "",
                "status" => "success"
            ];
        }
    }

    public function logout(Request $request){
        $user = User::whereRememberToken($request->token)
            ->update([
                "remember_token"=>""
            ]);
        return [
            "msg" => "Telah berhasil keluar",
            "errors" => "",
            "status" => "success"
        ];
    }
}
