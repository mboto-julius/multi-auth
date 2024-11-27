<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountVerificaion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class UserAuthController extends Controller
{
    public function registration(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'User already exists in our records',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();
        $user->notify((new AccountVerificaion($token, $user))->delay(now()->addSeconds(3)));
        return redirect('/login')->with('success', 'We have successfully registered your account. Please check your email (' . $user->email . ') to activate your account.');
    }  

    public function verifyEmail($token)
    {
        $user = User::where('remember_token', $token)->first();
        if (!$user) {
            return redirect('/login')->withErrors(['error' => 'Invalid verification token!']);
        }
        if ($user->email_verified_at) {
            return redirect('/login')->withErrors(['error' => 'Your account is already verified. Please log in.']);
        }
        $user->email_verified_at = now();
        $user->status = 1;
        $user->remember_token = null; 
        $user->save();
        return redirect('/login')->with('success', 'Your account has been successfully verified. Log in to continue!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login')->with('success', 'Successfully logged out');
    }
}
