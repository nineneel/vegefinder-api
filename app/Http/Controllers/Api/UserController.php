<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avatar;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * index
     *
     * Get a logged user.
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->with('avatar')->first();
        $response = $user->only('id', 'name', 'email', 'api_token');

        if ($user->avatar != null) {
            $avatar = $user->avatar->file_name;
            $response['avatar'] = $avatar;
        }

        return response()->json($response);
    }

    /**
     * register
     *
     * Register new user.
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'email' => "required|email|unique:users",
            'password' => "required|string|min:6|confirmed",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => "register new user failed",
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

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
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'user created successfully',
            'result' => $newUser
        ], 201);
    }

    /**
     * login
     *
     *
     *
     * @param  mixed $request
     * @return JsonResponse
     */
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

            $user_data = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "api_token" => $token
            ];

            if ($user->avatar != null) {
                $avatar = $user->avatar->file_name;
                $user_data['avatar'] = $avatar;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Login successfully',
                'token' => $token,
                'user' => $user_data
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Login failed, email & password did\'t match'
        ], 400);
    }

    /**
     * logout
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        User::find($request->user()->id)->forceFill(['api_token' => null])->save();

        return response()->json([
            'message' => "logout success"
        ]);
    }

    public function getAvatars(): JsonResponse
    {
        $avatars = Avatar::all();
        return response()->json($avatars);
    }
}
