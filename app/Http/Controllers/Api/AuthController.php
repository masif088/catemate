<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
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
        $user->update(['remember_token' => $token]);
        $respon = [
            'status' => 'success',
            'msg' => 'Berhasil masuk',
            'errors' => null,
            'content' => [
                'status_code' => 200,
                'access_token' => $user->remember_token,
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

    public function updateProfilePhoto(Request $request)
    {
        $file = $request->file('file');
        $filename = Str::slug($request->id . '-' . date('Hms')) . '.' . $request->file('file')->getClientOriginalExtension();
        Storage::disk('local')->put('public/profile_photo/' . $filename, file_get_contents($file));

        User::find(str_replace('"', '', $request->id))->update([
            'photo' => 'profile_photo/' . $filename
        ]);
        $user=User::find(str_replace('"', '', $request->id));
//        return [
//            "msg" => "Berhasil mengubah photo profil",
//            "errors" => "",
//            "status" => "success"
//        ];

        $response = [
            'status' => 'success',
            'msg' => 'Berhasil mengubah',
            'errors' => null,
            'content' => [
                'status_code' => 200,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ];
        return $response;
    }

    public function updateProfile(Request $request)
    {
        if ($request->password == null) {
            User::find($request->id)->update([
                "name" => $request->name,
                "address" => $request->address,
            ]);
        } else {
            User::find($request->id)->update([
                "name" => $request->name,
                "address" => $request->address,
                "password" => bcrypt($request->password),
            ]);
        }
        $user=User::find(str_replace('"', '', $request->id));
        $response = [
            'status' => 'success',
            'msg' => 'Berhasil mengubah',
            'errors' => null,
            'content' => [
                'status_code' => 200,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ];
        return $response;
    }

    public function updateLocation(Request $request)
    {
        $user=User::find($request->id);
        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return [
            "msg" => "Berhasil menyesuaikan lokasi",
            "errors" => "",
            "status" => "success",
            'content' => [
                'status_code' => 200,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ];
    }

    public function logout(Request $request)
    {
        $user = User::whereRememberToken($request->token)
            ->update([
                "remember_token" => ""
            ]);
        return [
            "msg" => "Telah berhasil keluar",
            "errors" => "",
            "status" => "success"
        ];
    }

    public function updateStatus(Request $request){
        $user=User::findOrFail($request->id);
        Log::create(['log'=>$request->status,]);
        $user->update([
            'status' => $request->status,
        ]);
        return [
            "msg" => "Berhasil mengubah status",
            "errors" => "",
            "status" => "success",
            'content' => [
                'status_code' => 200,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ];
    }
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', trans($response));
    }
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }
    public function broker()
    {
        return Password::broker();
    }
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

}
