<?php

namespace App\Http\Controllers;

use App\Models\AuthModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function loadLoginView()
    {
        return view('templates.admin.login');
    }

    public function loadRegisterView()
    {
        return view('templates.admin.register');
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = AuthModel::where('auth_user_email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->auth_user_password)) {
            return back()->with('error', 'Invalid email or password');
        }

        $token = JWTAuth::fromUser($user);

        $cookie = cookie(
            'auth_token',
            $token,
            60 * 24,
            '/',
            null,
            // app()->environment('production'),
            false,
            true,
            false,
            'Strict'
        );

        return redirect()->route('adminDashboard')
            ->withCookie($cookie)
            ->with('success', 'Login successful!');
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->cookie('auth_token');
            if ($token) {
                JWTAuth::setToken($token)->invalidate();
            }
        } catch (\Exception $e) {
        }

        return redirect()->route('adminLoginViewPage')
            ->withCookie(cookie()->forget('auth_token'))
            ->with('success', 'Logged out successfully!');
    }
}
