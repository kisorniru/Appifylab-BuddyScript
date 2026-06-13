<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class HomeController extends Controller
{
    private $admin;

    public function __construct()
    {
        $this->admin = json_decode(file_get_contents(public_path('/static/contact-info.json')));
    }

    public function landingPage()
    {
        return Inertia::render('Landing', [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'phone' => $this->admin->phone,
        ]);
    }

    public function login()
    {
        return Inertia::render('Login', [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'phone' => $this->admin->phone,
        ]);
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials is invalid.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
