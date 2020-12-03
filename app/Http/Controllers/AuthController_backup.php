<?php

namespace App\Http\Controllers;

use App\Http\Controllers\User\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{

    // const MODEL = "App\AuthController";

    // use RESTActions;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->middleware('role', ['except' => ['login','me']]);
    }

    public function test() {
        return response()->json(["msg" => "test ok !!"]);
    }
    public function login(Request $request)
    {
        // $email = $request->input('email');
        // $password = $request->input('password');
        // $credentials[] = $email;
        // $credentials[] = $password;
        $credentials = $request->only(['email', 'password']);
        // print_r($credentials);
        if (!$token = Auth::guard('api')->claims(['email' => $request->input('email')])->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        JWTAuth::setToken($token);
        $user = JWTAuth::toUser($token);
        $userData = UsersController::getUserByEmail($user['email']);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $userData,
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 
        ]);
    }

    public static function me() {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(["msg" => 'user_not_found'], 404);
        }
        $email = JWTAuth::getPayload()->get('email');
        $user = user::where('email',$email)->first();
        return $user;
    }
    
    public function infos() {
        return response()->json(['msg' => 'Data ok!']);
    }
}
