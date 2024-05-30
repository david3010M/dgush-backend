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

//        return response()->json(['user' => $user->typeuser->access], 200);

        // Obtiene el nombre de la ruta
        $routeName = $request->route()->getName();

        // Busca el OptionMenu correspondiente a la ruta
//        $optionMenu = OptionMenu::where('route', $routeName)->first();

//        Busca el Permission correspondiente a la ruta
        $permission = Permission::where('route', $routeName)->first();

//        return response()->json([
//            'permission' => $permission,
//            'user' => $user,
//            'typeuser' => $user->typeuser,
//            'hasPermission' => $user->typeuser->hasPermission,
//
//        ], 200);

        // Si no existe el OptionMenu, devuelve un error 404
        if (!$permission) {
            return response()->json(['error' => 'Route not found.'], 404);
        }

        // Verifica si el usuario tiene acceso a la opción de menú
        if ($user && $user->typeuser->hasPermission->contains('permission_id', $permission->id)) {
            return $next($request);
        }

        // Si no tiene acceso, devuelve un error 403
        return response()->json(['error' => 'Access Denied.'], 403);
    }
}
