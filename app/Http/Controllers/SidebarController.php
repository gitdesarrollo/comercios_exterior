<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SidebarController extends Controller
{
    protected function getSidebarItemParents()
    {
        $idUser = Auth::user()->id;
        $menu = DB::select("SELECT vu.id, vu.description FROM  view_users vu
                            INNER JOIN  user_has_views uv ON uv.permits = vu.id
                            INNER JOIN  roles_users rol ON uv.rol = rol.id
                            INNER JOIN  user_has_roles uhr ON uhr.idRoles = rol.id
                            WHERE idPadre IS NULL and uhr.idUser = :id",['id' => $idUser]);

        return response()->json($menu,200);
    }

    protected function getSidebarItemChildren(Request $request)
    {
        $idUser = Auth::user()->id;
        $menu = DB::select("SELECT vu.description FROM  view_users vu
                            INNER JOIN  user_has_views uv ON uv.permits = vu.id
                            INNER JOIN  roles_users rol ON uv.rol = rol.id
                            INNER JOIN  user_has_roles uhr ON uhr.idRoles = rol.id
                            WHERE idPadre = :idPadre and uhr.idUser = :id",['id' => $idUser, 'idPadre' => $request->idPadre]);

        return response()->json($menu,200);
    }
}
