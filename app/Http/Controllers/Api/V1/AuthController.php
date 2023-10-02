<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        //Input validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) return response()->json(['message' =>  $validator->errors()->first() ?? ''], 400);

        try {

            //Create user
            $user = new User([
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->save();

            return response()->json([
                'user_id' => $user->id,
                'status' => 'success',
                'message' => 'Registrasi berhasil',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'INTERNAL SERVER ERROR'], 500);
        }
    }

    public function login(Request $request)
    {
        // Input Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) return response()->json(['message' =>  $validator->errors()->first() ?? ''], 400);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
