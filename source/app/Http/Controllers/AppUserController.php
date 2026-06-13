<?php

namespace App\Http\Controllers;

use App\Constants\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AppUserController extends Controller
{
    public function userLogin()
    {
        return Inertia::render('User/Login');
    }

    public function userLoginPost(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'account_status' => AccountStatus::ACTIVE,
            'is_admin' => false,
        ];

        if (! Auth::guard('user')->attempt($credentials)) {
            return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();

        return redirect()->route('account.delete');
    }
}
