<?php

namespace App\Http\Middleware;

use App\Models\OptionMenu;
use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

//        GET THE ROUTE NAME
        $routeName = $request->route()->getName();

//        CHECK IF THE USER IS AN ADMIN
        if ($user->typeuser_id == 1) {
            return $next($request);
        } else {
//        RETURN  ERROR
            return response()->json(['error' => 'Access Denied.'], 403);
        }

    }
}
