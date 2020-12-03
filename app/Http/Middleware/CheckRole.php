<?php

namespace App\Http\Middleware;

use Closure;
// use JWTAuth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\AuthController;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle($request, Closure $next)
    // {
    //     if (!$user = JWTAuth::parseToken()->authenticate()) {
    //         return response()->json(['error' => 'User Not Found'], 404);
    //     }
    //     $actions = $request->route()[1];
    //     // list($found, $routeInfo, $params) = $request->route() ?: [false, [], []];
    //     // dd($params);
    //     // var_dump($request->route()[1]['roles']);
    //     // print_r($request->route());
    //     $roles = isset($actions['roles']) ? $actions['roles'] : null;
    //     if ($user->hasAnyRole($roles) || !$roles) {
    //         return $next($request);
    //     }
    //     return response()->json(["msg" => "Insufficient permissions"], 401);
    // }

    public function handle($request, Closure $next, ... $roles)
    {
        $user = AuthController::me();
	//echo $user;
	//echo $user->role_id;
        foreach($roles as $role) {
       
        if($user->hasRole($role))
            return $next($request);
        }

        return response()->json(["msg" => "Insufficient permissions"], 401);
    }
}
