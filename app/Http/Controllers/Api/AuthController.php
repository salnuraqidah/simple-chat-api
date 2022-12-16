<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }
     
        $user = User::where('email', $request->email)->first();
     
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }
        $user->tokens()->delete();
     
        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [
            "user" => $user,
            "token" => $token,
            "token_type" => 'Bearer'
        ];
        return response()->json(['status' => true, 'message' => 'success', 'data' => $data], 200);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
