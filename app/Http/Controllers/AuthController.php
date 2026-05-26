<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect('/dashboard-general-dashboard');
        }

        return view('pages.auth-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            if (auth()->user()->role === 'employee' || auth()->user()->role === 'talent acquisition') {
                return redirect()->intended('/my-attendance');
            }
            return redirect()->intended('/dashboard-general-dashboard');
        }

        return back()->withErrors([
            'email' => 'Incorrect email or password'
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
