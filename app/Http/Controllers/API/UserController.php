<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request){
       try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        //sensitif
        $credential = request(['email','password']);

        if(!Auth::attempt($credential)){
            return ResponseFormatter::error([
                'messsage' => 'Unauthorized',
            ], 'Authentication Failed', 500);
        }
        //get email
        $user = User::where('email', $request->email)->first();

        if(!Hash::check($request->password, $user->password, [])){
            throw new \Exception('Invalid Credentials');
        };

        $tokenresult = $user->createToken('authToken')->plainTextToken;

        return ResponseFormatter::success([
            'token' => $tokenresult,
            'type_token' => 'Bearer',
            'user' => $user    
        ], 'Authenticate');

       } catch (Exception $error) {
            return ResponseFormatter::error([
                'messsage' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
       } 
    }

    public function register(Request $request){
        try {
            $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
                'email' => ['required','email', 'string', 'min:3', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password],
                'phone' => ['nullable', 'string', 'min:3', 'max:255']
            ]);
        
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
            ]);

            $user = User::where('email', $request->email)->first();
            
            $tokenresult = $user->createToken('authToken')->plainTextToken;

            //tampil
            return ResponseFormatter::success([
                'token' => $tokenresult,
                'type_token' => 'Bearer',
                'user' => $user
            ], 'Register Success');

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Wrong',
                'error' => $error
            ], 'Authentication failed', 400);
        }
    }

    public function fetch(Request $request){
        return ResponseFormatter::success($request->user(), 'Data profile user berhasil diambil');
    }

    public function updateProfile(Request $request){
        try {
            $data = $request->validate([
                'email' => ['email','required','min:3', 'max:255', 'unique:users'],
                'name' => ['required','min:3', 'max:255'],
                'username' => ['required','min:3', 'max:255', 'unique:users'],
                'phone' => ['required','min:3', 'max:255'],
            ]);
            
            //ambil data semua
            $user = Auth::user();

            $user->update($data);

            return ResponseFormatter::success([
                $user
            ], 'User terupdate');
        } catch (Exception $error) {
            Return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Update profile failed', 500);
        }
        
    }

    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }
}
