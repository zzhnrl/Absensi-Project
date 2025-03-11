<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected function showLoginForm()
    {
        $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
        $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

        return view('vendor.adminlte.auth.login', compact('login_url', 'password_reset_url'));
    }

    protected function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = 1;
        $credentials['deleted_at'] = null;

        $failed_message = 'Oppes! You have entered invalid credentials';

        if (Auth::attempt($credentials)) {
            return redirect()->route('home');
        }
        return redirect()->back()->withErrors(['email' => $failed_message]);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
