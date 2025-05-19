<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $failed_message = 'Oppes! You have entered invalid credentials';

        // 1. Ambil user yang aktif & belum dihapus
        $user = User::where('email', $request->email)
                    ->where('is_active', 1)
                    ->whereNull('deleted_at')
                    ->first();

        if (! $user) {
            return redirect()->back()->withErrors(['email' => $failed_message]);
        }

        // 2. Decrypt password dari DB
        try {
            $decrypted = Crypt::decryptString($user->password);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => $failed_message]);
        }

        // 3. Cocokkan input dengan hasil decrypt
        if ($request->password === $decrypted) {
            Auth::login($user, $request->filled('remember'));
            return redirect()->route('home');
        }

        return redirect()->back()->withErrors(['email' => $failed_message]);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
