<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    private $ADMIN_USER = 'admin';
    private $ADMIN_PASS = '12345';
    // ---------------------------------------------

    /**
     * Muestra el formulario de login.
     */
    public function showLogin()
    {
        // Si ya está logueado, que no vea el login, que vaya al dashboard
        if (Session::get('admin_logged_in') === true) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login'); // Apunta a la vista que crearemos
    }

    /**
     * Procesa el intento de login.
     */
    public function doLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 3. Compara con las credenciales quemadas
        if ($request->username === $this->ADMIN_USER && $request->password === $this->ADMIN_PASS) {

            // 4. ¡Éxito! Guarda en la sesión que el admin está logueado
            $request->session()->put('admin_logged_in', true);

            // Redirige al dashboard protegido
            return redirect()->route('admin.dashboard');
        }

        // 5. ¡Fallo! Regresa al login con un mensaje de error
        return back()->withErrors([
            'message' => 'Credenciales incorrectas. Por favor intente de nuevo.',
        ]);
    }

    /**
     * Cierra la sesión del admin.
     */
    public function logout(Request $request)
    {
        // Borra la variable de sesión
        $request->session()->forget('admin_logged_in');

        // Redirige al formulario de login
        return redirect()->route('admin.login.form');
    }
}