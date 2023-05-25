<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request): User
    {
        return $request->user();
    }

    public function register(Request $request): User
    {
        $validatedData = $request->validate([
            'name' => "required",
            'email' => "required|email|unique:users",
            'password' => "required|string|min:6|confirmed",
        ]);

        $validatedData['register_method'] = 'manual';
        $validatedData['avatar_id'] = $request->avatar;
        $validatedData['password'] = bcrypt($request->password);

        $newUser = User::create($validatedData);

        return $newUser;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = md5(time()) . '.' . md5($request->email);

            User::find($user->id)->forceFill(['api_token' => $token])->save();

            return response()->json([
                'token' => $token
            ]);
        }

        return response()->json([
            "message" => "Email dan Password yang diberikan tidak sesuai!"
        ]);
    }

    public function logout(Request $request)
    {
        User::find($request->user()->id)->forceFill(['api_token' => null])->save();

        return response()->json([
            'message' => "logout success"
        ]);
    }
}
