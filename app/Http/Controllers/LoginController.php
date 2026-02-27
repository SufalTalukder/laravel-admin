<?php

namespace App\Http\Controllers;

use App\Models\AuthModel;
use App\Models\SystemActivityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
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

        $agent = new Agent();
        $ip = $request->ip();
        $location = Location::get($ip);

        $user = AuthModel::where('auth_user_email', $request->email)->first();

        // Prepare common audit data
        $auditData = [
            'auth_method'        => 'email_password',
            'browser'            => $agent->browser(),
            'browser_version'    => $agent->version($agent->browser()),
            'device_type'        => $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
            'device_model'       => $agent->device(),
            'ip_address'         => $ip,
            'operating_system'   => $agent->platform(),
            'os_version'         => $agent->version($agent->platform()),
            'user_agent'         => $request->userAgent(),
            'possible_incognito' => false,
            'referrer_url'       => $request->headers->get('referer'),
            'session_id'         => session()->getId(),
            'login_time'         => now()
        ];

        $geoData = [
            'country' => optional($location)->countryName,
            'state'   => optional($location)->regionName,
            'city'    => optional($location)->cityName,
            'lat'     => optional($location)->latitude,
            'long'    => optional($location)->longitude,
            'address' => optional($location)->cityName
                ? optional($location)->cityName . ', ' .
                optional($location)->regionName . ', ' .
                optional($location)->countryName
                : null,
        ];

        if (!$user || !Hash::check($request->password, $user->auth_user_password)) {

            SystemActivityModel::create(array_merge($auditData, $geoData, [
                'auth_user_id'  => $user->auth_user_id ?? 0,
                'login_status'  => 'failed',
                'failure_reason' => 'Invalid email or password'
            ]));

            return back()->with('error', 'Invalid email or password');
        }

        $token = JWTAuth::fromUser($user);

        $cookie = cookie(
            'auth_token',
            $token,
            60 * 24,
            '/',
            null,
            false,
            true,
            false,
            'Strict'
        );

        SystemActivityModel::create(array_merge($auditData, $geoData, [
            'auth_user_id' => $user->auth_user_id,
            'login_status' => 'success',
            'failure_reason' => null
        ]));

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
