<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => "required",
            'email' => "required|email|unique:users",
            'password' => "required|string|min:6|confirmed",
        ]);

        $validatedData['register_method'] = 'manual';
        $validatedData['avatar_id'] = $request->avatar;
        $validatedData['password'] = bcrypt($request->password);

        DB::beginTransaction();

        try {
            $newUser = User::create($validatedData);

            UserRole::create([
                'user_id' => $newUser->id,
                'role_id' => 2, // normal user
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User Created Successfully',
            'result' => $newUser
        ], 201);
    }

    public function login(Request $request): JsonResponse
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
