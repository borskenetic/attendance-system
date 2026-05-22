<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            $role = auth()->user()->role;
            if (in_array($role, ['admin', 'staff'], true)) {
                return redirect()->route('home');
            }

            return $this->redirectForRole($role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return $this->redirectForRole(Auth::user()->role);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectForRole(?string $role)
    {
        return match ($role) {
            'admin', 'staff' => redirect()->route('home'),
            'student', 'faculty' => redirect()->route('attendance.scan'),
            default => redirect()->route('login')->with('error', 'Unauthorized role.'),
        };
    }
}
