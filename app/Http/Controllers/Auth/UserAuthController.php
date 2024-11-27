<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountVerificaion;
use App\Notifications\UserResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
        $user = User::whereEmail($request->email)->first();

        if(!$user){
            Session::flash('error', 'The provided credentials do not match our records.');
            return back();
        }

        if($user->status == 0){
            Session::flash('error', 'Your account is deactivated. Please contact administrator');
            return back();
        }        

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

    public function forgetPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'User does not exist in our records.',
        ]);

        $user = User::where('email', $request->email)->where('status', 1)->first();
        if (!$user) {
            return back()->with('error', 'User does not exist in our records or your account is not active.');
        }

        $existingToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if ($existingToken) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->update([
                    'token' => Str::random(120),
                    'created_at' => now()
                ]);
        } else {
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Str::random(120),
                'created_at' => now()
            ]);
        }

        $tokenInfo = DB::table('password_reset_tokens')
            ->where('email', $request->email)->first();

        $token = $tokenInfo->token;

        $user->notify((new UserResetPassword($token, $user))->delay(now()->addSeconds(3)));
        return back()->with('success', 'We have successfully sent the password reset link to ' . $user->email);
    }

    public function resetPassword($tokenValue)
    {
        $tokenInfo = DB::table('password_reset_tokens')
            ->where('token', $tokenValue)
            ->first();

        if (!$tokenInfo) {
            Session::flash('error', 'Token is expired or invalid!');
            return redirect()->route('admin.login');
        }
        return view('user.auths.reset-password', compact('tokenInfo'));
    }

    public function resetPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $password = $request->password;
        $tokenInfo = DB::table('password_reset_tokens')
            ->where([
                'token' => $request->token,
                'email' => $request->email
            ])->first();

        if (!$tokenInfo) {
            Session::flash('error', 'Invalid token associated with that email!');
            return back();
        }

        $user = User::where('email', $tokenInfo->email)->first();

        if ($user) {
            $user->password = bcrypt($password);
            $user->save();

            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->delete();

            return redirect('/login')->with('success', 'Password changed successfully');
        } else {
            Session::flash('error', 'Token is invalid. Please try to reset again');
            return back();
        }
    }
}
