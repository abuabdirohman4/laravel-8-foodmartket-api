<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request) {
        try {
            // Validation Input
            $request->validate([
                'email' => 'email:dns|required',
                'password' => 'required'
            ]);

            // Check credentials (login)
            $credentials = request(['email','password']);
            if(!Auth::atempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            // If hash now match then give error
            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password, [])){
                throw ne\Exception("invalid Credentials");
            }

            // If success then logged
            $tokenResult = $user-> createToken('authToken')->plainTextToken;
            return ResponseFormater::success([
                'acess_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch(Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Sometrhing went wrong',
                'error' => $error
            ], 'Authenticated Failed', 500);
        }
    }
}
