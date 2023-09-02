<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubject;
use Carbon\Carbon;
use App\Models\User;
use JWTAuth;
use DB;

class UserC
{
    public function login(Request $req){
        $credentials=$req->only('username','password');
        $token = JWTAuth::attempt($credentials);

        try {
            if(!$token){
                return response()->json(['error' => 'Invalid credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error'=>'Could Not Create Token'], 500);
        }

        $cookie = cookie('token:root', $token);
        
        return response()->json(['data'=>[
            "message"=>'Login Berhasil',
            "access_token" => $token,
        ]])->withCookie($cookie);
    }

    public function registerAdmin(Request $req){
        $validate = Validator::make($req->all(), [
            'nama' => 'required|string|max:500',
            'no_hp' => 'required|numeric|min:11',
            'email' => 'required|string|max:255|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:4|confirmed'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors()->toJson(), 400);
        }else{
            $data = $req->input();
            try {
                $user = new User;
                $user->nama = $data['nama'];
                $user->alamat = null;
                $user->no_hp = $data['no_hp'];
                $user->email = $data['email'];
                $user->username = $data['username'];
                $user->password = Hash::make($data['password']);
                $user->role = 'admin';
                $user->created_by = 0;
                $user->created_at = Carbon::now();
                $user->updated_by = 0;
                $user->updated_at = null;
                $user->save();
                $token = JWTAuth::fromUser($user,[
                    'id' => $user->id,
                    'role' => $user->role
                ]);
                return response()->json(['data'=>[
                    "message" => 'Data berhasil ditambah',
                    "token" => $token
                ]], 200);
            } catch (Exception $e) {
                return response()->json(['error'=>'Data gagal ditambah'], 500);
            }
        }
    }

    public function registerPetugas(Request $req){
        $validate = Validator::make($req->all(), [
            'nama' => 'required|string|max:500',
            'alamat' => 'required|string|max:500',
            'no_hp' => 'required|numeric|min:11',
            'email' => 'required|string|max:255|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:4|confirmed'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors()->toJson(), 400);
        }else{
            $data = $req->input();
            try {
                $user = new User;
                $user->nama = $data['nama'];
                $user->no_hp = $data['no_hp'];
                $user->email = $data['email'];
                $user->username = $data['username'];
                $user->password = Hash::make($data['password']);
                $user->role = 'petugas';
                $user->created_by = JWTAuth::parseToken()->getPayload()->get('id');
                $user->created_at = Carbon::now();
                $user->updated_by = 0;
                $user->updated_at = null;
                $user->save();
                return response()->json(['message'=>'Data berhasil ditambah'], 200);
            } catch (Exception $e) {
                return response()->json(['error'=>'Data gagal ditambah'], 500);
            }
        }
    }

    public function registerPengguna(Request $req){
        $validate = Validator::make($req->all(), [
            'nama' => 'required|string|max:500',
            'alamat' => 'required|string|max:500',
            'no_hp' => 'required|numeric|min:11',
            'email' => 'required|string|max:255|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:4|confirmed'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors()->toJson(), 400);
        }else{
            $data = $req->input();
            try {
                $user = new User;
                $user->nama = $data['nama'];
                $user->no_hp = $data['no_hp'];
                $user->email = $data['email'];
                $user->username = $data['username'];
                $user->password = Hash::make($data['password']);
                $user->role = 'pengguna';
                $user->created_by = 0;
                $user->created_at = Carbon::now();
                $user->updated_by = 0;
                $user->updated_at = null;
                $user->save();
                return response()->json(['message'=>'Data berhasil ditambah'], 200);
            } catch (Exception $e) {
                return response()->json(['error'=>'Data gagal ditambah'], 500);
            }
        }
    }

    public function logout(Request $req){
        // Auth::logout();
        // $cookie = CookiEvent::forget('token:root');
        // $req->session()->invalidate();
        // $req->session()->regenerateToken();
        $cookie = Cookie::forget('token:root');
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message'=>'Berhasil logout'], 200)->withCookie($cookie);
        // return redirect('/');
    }

    public function getAuthenticatedUser() { 
        try { 
            if (! $user = JWTAuth::parseToken()->authenticate()) { 
                return response()->json(['user_not_found'], 404); 
            } 
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) { 
            return response()->json(['token_expired'], $e->getStatusCode()); 
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) { 
            return response()->json(['token_invalid'], $e->getStatusCode()); 
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) { 
            return response()->json(['token_absent'], $e->getStatusCode()); 
        } 
        return response()->json(compact('user')); 
    }
}
