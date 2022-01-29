<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            $tokenResult = $user->createToken('authToken')->plainTextToken;
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

    public function register(Request $request) {
        try {
            $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['requires','string','email', 'max:255','unique:users'],
                'password' => $this->passwordRules()
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'house_number' => $request->house_number,
                'phone_number' => $request->phone_number,
                'city' => $request->city,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }
}
