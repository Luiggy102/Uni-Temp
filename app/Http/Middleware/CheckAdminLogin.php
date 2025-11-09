<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Revisa si la variable de sesión 'admin_logged_in' existe y es 'true'
        if ($request->session()->get('admin_logged_in') !== true) {

            // Si no está logueado, lo redirige al formulario de login
            return redirect()->route('admin.login.form');
        }

        // Si sí está logueado, déjalo pasar a la ruta que quería
        return $next($request);
    }
}