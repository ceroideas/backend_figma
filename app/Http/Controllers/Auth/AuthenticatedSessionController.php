<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Autenticar al usuario
        $request->authenticate();
    
        // Verificar si el usuario autenticado es un administrador
        if (!auth()->user()->is_admin) {
            // Si no es administrador, cerrar sesión y redirigir al login con un mensaje de error
            Auth::logout();
            return Redirect::route('login')->withErrors([
                'email' => 'Debe ser un administrador para iniciar sesión.',
            ]);
        }
    
        // Regenerar la sesión para prevenir ataques de fijación de sesión
        $request->session()->regenerate();
    
        // Redirigir a la ruta que el usuario intentaba acceder originalmente o al home
        return redirect()->route('admin.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
