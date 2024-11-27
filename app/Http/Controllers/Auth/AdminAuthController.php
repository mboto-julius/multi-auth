<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login')->with('success', 'Successfully logged out');
    }

    public function forgetPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ], [
            'email.exists' => 'User does not exist in our records.',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            Session::flash('error', 'User does not exists in our records');
            return back();
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

        $admin->notify((new ResetPassword($token, $admin))->delay(now()->addSeconds(3)));
        return back()->with('success', 'We have successfully sent the password reset link to ' . $admin->email);
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
        return view('admin.auths.reset-password', compact('tokenInfo'));
    }

    public function resetPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
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

        $admin = Admin::where('email', $tokenInfo->email)->first();

        if ($admin) {
            $admin->password = bcrypt($password);
            $admin->save();

            DB::table('password_reset_tokens')
                ->where('email', $admin->email)
                ->delete();

            return redirect('/admin/login')->with('success', 'Password changed successfully');
        } else {
            Session::flash('error', 'Token is invalid. Please try to reset again');
            return back();
        }
    }
}
